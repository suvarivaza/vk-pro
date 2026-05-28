<?php

namespace Service\Posting;

use DateTime;

/**
 * @property \Lib_Curl $_curl
 */
class Controller_Shell_Posting extends Controller_Shell
{
    /** @var \Lib_Curl */
    private $_curl = null;

    public function __construct()
    {
        parent::__construct();
        $this->_curl = new \Lib_Curl();
    }

    public function A_Test()
    {
        $post = $this->factoryPosting->posts->getById(855, true);

        if ($post === null) {
            return;
        }
        $group = $this->factoryPosting->groups->getById($post->groupId, true);

        if ($group === null) {
            return;
        }
        $user = $this->factoryUsers->users->getById($group->userId, true);

        if ($user === null) {
            return;
        }

        $string = [];
        $attachments = $post->getAttachments();
        usort($attachments, [$this, '_sort_photos']);

        foreach ($attachments as $attachment) {
            switch ($attachment['type']) {
                case 'video':
                    $string[] = 'video' . $attachment['owner_id'] . '_' . $attachment['id'];
                    break;
                case 'doc':
                    $string[] = 'doc' . $attachment['doc']['owner_id'] . '_' . $attachment['doc']['id'];
                    break;
                case 'url':
                    $string[] = $attachment['url'];
                    break;
                case 'poll':

                    $response = $this->VK->pollsCreate($attachment['title'], $group->ownerId, 2, json_encode($attachment['answers'], JSON_UNESCAPED_UNICODE), $user->access_token);

                    if ($response['id'] > 0) {
                        usleep(200000);
                        $string[] = 'poll' . $response['owner_id'] . '_' . $response['id'];
                    }
                    break;
                case 'photo':

                    $response = $this->VK->getWallUploadServer($group->ownerId, $user->access_token);

                    sleep(1);

                    if ($response['upload_url']) {
                        $path = IMAGES_PATH . 'posting/big/' . $attachment['big']['path'];
                        $image = new \Imagick();
                        $image->setBackgroundColor(new \ImagickPixel('transparent'));
                        $image->readImage($path);

                        if ($group->isWatermark & 4) {
                            $this->_getPhotoDataText($group, $image);
                        }

                        if ($group->isWatermark & 2) {
                            $this->_getPhotoDataWatermark($group, $image);
                        }
                        $image->setImageFormat('jpg');
                        $image->writeImage(IMAGES_PATH . 'posting/temp/posting.jpg');

                        $filedata = IMAGES_PATH . 'posting/temp/posting.jpg';

                        $json = $this->VK->uploadPhotoToServerVk($response['upload_url'], $filedata);

                        if ($json['server']) {

                            $final = $this->VK->saveWallPhoto($group->ownerId, $json['photo'], $json['server'],$json['hash'], $user->access_token);

                            $string[] = 'photo' . $final[0]['owner_id'] . '_' . $final[0]['id'];
                            usleep(200000);
                        }
                    }
            }
        }

        if ($group->hashtags) {
            if ($group->hashtagsPos) {
                $post->text .= "\n" . $group->hashtags;
            } else {
                $post->text = $group->hashtags . "\n" . $post->text;
            }
        }

        $params = [
            'owner_id' => '-' . $group->ownerId,
            'from_group' => 1,
            'message' => $post->text ?: ' ',
            'signed' => $post->signature ? 1 : 0,
            'access_token' => $user->access_token,
        ];

        if (count($string)) {
            $params['attachments'] = implode(',', $string);
        }

        if ($post->ads) {
            $params['mark_as_ads'] = 1;
        }

        $response = $this->VK->addPost($params);

        if ($response['post_id'] > 0) {
            $post->isPost = true;
            $post->isPostDate = time();
        } elseif ($response['error'] == 5) {
            $user->token_require = true;
            $this->factoryUsers->users->save($user);
        }

        if ($this->factoryPosting->posts->save($post)) {
            $factoryMessages = new \Service\Messages\Model_Factory();
            $mConfig = \Service\Messages\Model_Config::GetConfig();

            $message = $factoryMessages->users->getNew();
            $message->userId = $group->userId;
            $message->isDone = false;
            $message->type = \Service\Messages\Model_Config::TYPE_SYSTEM;
            $text = $mConfig['posting']['types']['publish']['text'];
            $text = str_replace('%group_link%', $group->url, $text);
            $message->text = $text;

            $message->icon = 'posting';
            $factoryMessages->users->save($message);

            $group->datePost = time();

            if ($group->isFree) {
                --$group->isFreeCount;
            }
            $this->factoryPosting->groups->save($group);
        }
    }

    private function _getPhotoDataText(Model_Groups_Group $group, $image)
    {
        $drawText = new \ImagickDraw();
        $pixelText = new \ImagickPixel($group->watermarkColor);

        /* Black text */
        $drawText->setFillColor($pixelText);
        $drawText->setFillAlpha(1 - $group->watermarkTextOpacity);

        /* Font properties */
        $drawText->setFont('Bookman-DemiItalic');
        $drawText->setFontSize($group->watermarkSize);

        $drawText->setFont(ENGINE_PATH . 'fonts/imagick/' . $group->watermarkFont . '.ttf');
        $arr = $image->queryFontMetrics($drawText, $group->watermarkText);

        $x = 0;
        $y = 0;

        switch ($group->watermarkTextPos) {
            case 0:
                $x = $image->getImageWidth() / 2 - $arr['textWidth'] / 2;
                $y = $image->getImageHeight() / 2;
                break;
            case 1:
                $x = $image->getImageWidth() - $arr['textWidth'];
                $y = $image->getImageHeight();
                break;
            case 2:
                $x = 0;
                $y = $image->getImageHeight();
                break;
            case 3:
                $x = 0;
                $y = $arr['textHeight'];
                break;
            case 4:
                $x = $image->getImageWidth() - $arr['textWidth'];
                $y = $arr['textHeight'];
                break;
            case 5:
                $x = $image->getImageWidth() / 2 - $arr['textWidth'] / 2;
                $y = $arr['textHeight'];
                break;
            case 6:
                $x = 0;
                $y = $image->getImageHeight() / 2;
                break;
            case 7:
                $x = $image->getImageWidth() - $arr['textWidth'];
                $y = $image->getImageHeight() / 2;
                break;
            case 8:
                $x = $image->getImageWidth() / 2 - $arr['textWidth'] / 2;
                $y = $image->getImageHeight();
        }
        $image->annotateimage($drawText, $x, $y, 0, $group->watermarkText);

        return $image->getImageBlob();
    }

    private function _getPhotoDataWatermark(Model_Groups_Group $group, $image)
    {
        $watermark = new \Imagick();
        $watermark->setBackgroundColor(new \ImagickPixel('transparent'));
        $watermark->readImage(IMAGES_PATH . 'grabber/watermark/' . $group->watermark);
        $w = min($watermark->getImageWidth(), $image->getImageWidth() * ($group->watermarkMaxSize / 100));
        $h = min($watermark->getImageHeight(), $image->getImageHeight());

        $src_ratio = $watermark->getImageWidth() / $watermark->getImageHeight();
        $new_w = $w;
        $new_h = $h;
        $ratio = $w / $h;

        if ($ratio < $src_ratio) {
            $new_h = $new_w / $src_ratio;
        } else {
            $new_w = $new_h * $src_ratio;
        }

        $watermark->scaleImage($new_w, $new_h);
        $watermark->evaluateImage(\Imagick::EVALUATE_MULTIPLY, 1 - $group->watermarkOpacity, \Imagick::CHANNEL_ALPHA);

        $x = 0;
        $y = 0;

        switch ($group->watermarkPos) {
            case 0:
                $x = $image->getImageWidth() / 2 - $watermark->getImageWidth() / 2;
                $y = $image->getImageHeight() / 2 - $watermark->getImageHeight() / 2;
                break;
            case 1:
                $x = $image->getImageWidth() - $watermark->getImageWidth();
                $y = $image->getImageHeight() - $watermark->getImageHeight();
                break;
            case 2:
                $x = 0;
                $y = $image->getImageHeight() - $watermark->getImageHeight();
                break;
            case 3:
                $x = 0;
                $y = 0;
                break;
            case 4:
                $x = $image->getImageWidth() - $watermark->getImageWidth();
                $y = 0;
                break;
            case 5:
                $x = $image->getImageWidth() / 2 - $watermark->getImageWidth() / 2;
                $y = 0;
                break;
            case 6:
                $x = 0;
                $y = $image->getImageHeight() / 2 - $watermark->getImageHeight() / 2;
                break;
            case 7:
                $x = $image->getImageWidth() - $watermark->getImageWidth() / 2;
                $y = $image->getImageHeight() / 2 - $watermark->getImageHeight() / 2;
                break;
            case 8:
                $x = $image->getImageWidth() / 2 - $watermark->getImageWidth() / 2;
                $y = $image->getImageHeight();
        }

        $image->compositeImage($watermark, \Imagick::COMPOSITE_DEFAULT, $x, $y);
    }

    public function A_Run()
    {

        //Установим время и дату для записи в лог скрипта
        $date = new DateTime();
        $date = $date->format("Y-m-d H:i:s");
        $tm = time();

        //Зададим переменные для отслеживаемых метрик скрипта
        $countActiveGroups = 0;
        $countPosts = 0;
        $page = 0;

        do {
            $count = 0;
            $query = $this->factoryPosting->posts->query()->limit(1000)->offset($page * 1000)->sort('postId', 'ASC');
            $query->filter->fieldValue('isPost', '=', false);
            //выбираем только посты которые уже пора публиковать (у которых время публикации задано меньше чем сейчас + 30 секунд)
            $query->filter->fieldValue('datePost', '<', time() + 30);
            //$query->filter->fieldValue('userId', '=', \Config::$adminId); //для тестов

            $posts = $query->iteratorForSave();

            /** @var Model_Posts_Post $post */
            foreach ($posts as $post) {
                $count++;

                $group = $this->factoryPosting->groups->getById($post->groupId, true);

                if ($group === null) {
                    continue;
                }

                if ($group->isFree && $group->isFreeCount < 1) {
                    continue;
                }

                if (!$group->isActive || !$group->userActive) {
                    continue;
                }

                if ($group->timeLimit) {
                    $now = time();
                    $from = mktime($group->timeHourFrom, $group->timeMinuteFrom, 0, date('m'), date('d'), date('Y'));
                    $to = mktime($group->timeHourTo, $group->timeMinuteTo, 0, date('m'), date('d'), date('Y'));

                    if ($now > $from && $now < $to) {
                        continue;
                    }
                }

                $user = $this->factoryUsers->users->getById($group->userId, true);

                if (!$user) {
                    continue;
                }


                $countActiveGroups++;


                $string = [];
                $attachments = $post->getAttachments();
                usort($attachments, [$this, '_sort_photos']);

                foreach ($attachments as $attachment) {
                    switch ($attachment['type']) {
                        case 'video':
                            $string[] = 'video' . $attachment['owner_id'] . '_' . $attachment['id'];
                            break;
                        case 'doc':
                            $string[] = 'doc' . $attachment['doc']['owner_id'] . '_' . $attachment['doc']['id'];
                            break;
                        case 'url':
                            $string[] = $attachment['url'];
                            break;
                        case 'poll':

                            $response = $this->VK->pollsCreate($attachment['title'], $group->ownerId, 2, json_encode($attachment['answers'], JSON_UNESCAPED_UNICODE), $user->access_token);

                            if ($response['id'] > 0) {
                                usleep(200000);
                                $string[] = 'poll' . $response['owner_id'] . '_' . $response['id'];
                            }
                            break;
                        case 'photo':
                            //Загружаем картинку на сервер ВК
                            //Получаем адрес сервера для загрузки
                            $response = $this->VK->getWallUploadServer($group->ownerId, $user->access_token);

                            //Если получили адрес для загрузки - загружаем
                            if (isset($response['upload_url']) and $response['upload_url']) {
                                $path = IMAGES_PATH . 'posting/big/' . $attachment['big']['path'];
                                $image = new \Imagick();
                                $image->readImage($path);

                                if ($group->isWatermark & 4) {
                                    $this->_getPhotoDataText($group, $image);
                                }

                                if ($group->isWatermark & 2) {
                                    $this->_getPhotoDataWatermark($group, $image);
                                }
                                $image->setImageFormat('jpg');
                                $image->writeImage(IMAGES_PATH . 'posting/temp/posting.jpg');

                                $filedata = IMAGES_PATH . 'posting/temp/posting.jpg';

                                //Формируем запрос для загрузки изображения на сервер ВК
                                $json = $this->VK->uploadPhotoToServerVk($response['upload_url'], $filedata);


                                //Если успешно загружено - сохраняем
                                if ($json['server']) {

                                    $final = $this->VK->saveWallPhoto($group->ownerId, $json['photo'], $json['server'],$json['hash'], $user->access_token);


                                    $string[] = 'photo' . $final[0]['owner_id'] . '_' . $final[0]['id'];
                                    usleep(200000);
                                }
                            } else {
                                continue 3;
                            }
                    }
                }

                if ($group->hashtags) {
                    if ($group->hashtagsPos) {
                        $post->text .= "\n" . $group->hashtags;
                    } else {
                        $post->text = $group->hashtags . "\n" . $post->text;
                    }
                }

                $params = [
                    'owner_id' => '-' . $group->ownerId,
                    'from_group' => 1,
                    'message' => $post->text,
                    'signed' => $post->signature ? 1 : 0,
                    'access_token' => $user->access_token,
                ];

                if (count($string)) {
                    $params['attachments'] = implode(',', $string);
                }

                if ($post->ads) {
                    $params['mark_as_ads'] = 1;
                }

                //Публикуем пост
                $response = $this->VK->addPost($params);

                if ($response['error'] > 0) {
                    usleep(500000);
                    continue;
                }

                if (isset($response['post_id']) and $response['post_id'] > 0) {
                    $countPosts++;
                    $post->isPost = true;
                    $post->isPostDate = time();
                } elseif ($response['error'] == 5) {
                    $user->token_require = true;
                    $this->factoryUsers->users->save($user);
                }

                if ($this->factoryPosting->posts->save($post)) {
                    $factoryMessages = new \Service\Messages\Model_Factory();
                    $mConfig = \Service\Messages\Model_Config::GetConfig();

                    $message = $factoryMessages->users->getNew();
                    $message->userId = $group->userId;
                    $message->isDone = false;
                    $message->type = \Service\Messages\Model_Config::TYPE_SYSTEM;
                    $text = $mConfig['posting']['types']['publish']['text'];
                    $text = str_replace('%group_link%', $group->url, $text);
                    $message->text = $text;

                    $message->icon = 'posting';
                    $factoryMessages->users->save($message);

                    $group->datePost = time();

                    if ($group->isFree) {
                        --$group->isFreeCount;
                    }
                    $this->factoryPosting->groups->save($group);
                }
                usleep(500000);
            }

            $page++;
        } while ($count);

        //Если есть опубликованные посты выведем для записи в лог скрипта
        if($countPosts){
            echo "\naction=Posting/Posting:Run";
            echo $date;
            echo "\nВремя выполнения: " . round((time() - $tm) / 60, 2);
            echo "\nВсего групп с активированным автопостингом: " . $countActiveGroups;
            echo "\nВсего опубликовано постов: " . $countPosts;
            echo "\n";
        }

    }

    public function A_UpdateActive()
    {

        //echo 'A_UpdateActive()';
        $query = $this->factoryPosting->groups->query()->limit(10000)->sort('groupId', 'ASC');
        $query->filter->fieldValue('isActive', '=', true)
            ->fieldValue('dateValid', '<', time());

        $it = $query->iteratorForSave();
        /** @var Model_Groups_Group $group */
        foreach ($it as $group) {
            $group->isActive = false;
            $this->factoryPosting->groups->save($group);
        }

        $query = $this->factoryPosting->groups->query()->limit(10000)->sort('groupId', 'ASC');
        $query->filter->fieldValue('isActive', '=', false)
            ->fieldValue('dateValid', '>', time());

        $it = $query->iteratorForSave();

        foreach ($it as $group) {
            $group->isActive = true;
            $this->factoryPosting->groups->save($group);
        }
    }

    protected function _sort_photos($a, $b)
    {
        if ($a['type'] == 'photo' && $b['type'] != 'photo') {
            return 1;
        }

        if ($a['type'] == 'photo' && $b['type'] == 'photo') {
            return $a['sort'] > $b['sort'];
        }

        return 0;
    }
}
