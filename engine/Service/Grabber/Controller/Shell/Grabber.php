<?php

namespace Service\Grabber;

use DateTime;
use Imagick;

class Controller_Shell_Grabber extends Controller_Shell
{
    /** @var \Lib_Curl */
    protected $_curl = null;

    public function __construct()
    {
        parent::__construct();
        $this->_curl = new \Lib_Curl();
    }

    public function A_RunOnce()
    {
        $query = $this->factoryGrabber->groups->query()->limit(500);
        $query->filter
            ->fieldValue('userId', '=', (int) $this->params['userId'])
            ->fieldValue('isActive', '=', true)
            ->fieldValue('userActive', '=', true);

        $it = $query->iteratorForSave();
        /** @var Model_Groups_Group $group */
        foreach ($it as $group) {
            if ($group->isFree && $group->isFreeCount < 1) {
                continue;
            }

            if ($group->random) {
                $posts = $this->factoryGrabber->posts->getByGroupIdIsPost($group->groupId, false, true, 100);
                shuffle($posts);
                array_slice($posts, 0, 1);
            } else {
                $posts = $this->factoryGrabber->posts->getByGroupIdIsPost($group->groupId, false, true, 1);
            }

            $user = $this->factoryUsers->users->getById($group->userId, true);

            if ($user->token_require) {
                continue;
            }

            if (!$user->access_token) {
                continue;
            }

            foreach ($posts as $post) {
                $string = [];
                $attachments = $post->getAttachments();

                foreach ($attachments as $attachment) {
                    switch ($attachment['type']) {
                        case 'video':
                            $string[] = 'video' . $attachment['video']['owner_id'] . '_' . $attachment['video']['id'];
                            break;
                        case 'doc':
                            $string[] = 'doc' . $attachment['doc']['owner_id'] . '_' . $attachment['doc']['id'];
                            break;
                        case 'link':
                            $string[] = $attachment['link']['url'];
                            break;
                        case 'poll':
                            $answers = [];

                            foreach ($attachment['poll']['answers'] as $answer) {
                                $answers[] = $answer['text'];
                            }

                            $response = $this->VK->pollsCreate($attachment['poll']['question'], $group->ownerId,2, json_encode($answers, JSON_UNESCAPED_UNICODE), $user->access_token);


                            if ($response['id'] > 0) {
                                usleep(200000);
                                $string[] = 'poll' . $response['owner_id'] . '_' . $response['id'];
                            }
                            break;
                        case 'photo':
                            $photoUrl = '';

                            //старый вариант
                            if (isset($attachment['photo']['photo_2560'])) {
                                $photoUrl = $attachment['photo']['photo_2560'];
                            } elseif (isset($attachment['photo']['photo_1280'])) {
                                $photoUrl = $attachment['photo']['photo_1280'];
                            } elseif (isset($attachment['photo']['photo_807'])) {
                                $photoUrl = $attachment['photo']['photo_807'];
                            } elseif (isset($attachment['photo']['photo_604'])) {
                                $photoUrl = $attachment['photo']['photo_604'];
                            } elseif (isset($attachment['photo']['photo_130'])) {
                                $photoUrl = $attachment['photo']['photo_130'];
                            }

                            //условие для нового API
                            if (!$photoUrl) {
                                $photo = max($attachment['photo']['sizes']); //Выбираем изображение с максимальным разрешением
                                $photoUrl = $photo['url'];
                            }

                            try {
                                $data = file_get_contents($photoUrl);
                                $image = new \Imagick();
                                $image->readImageBlob($data);
                            } catch (\Exception $e) {
                                continue 3;
                            }

                            if ($group->isWatermark & 4) {
                                $this->_getPhotoDataText($group, $image);
                            }

                            if ($group->isWatermark & 2) {
                                $this->_getPhotoDataWatermark($group, $image);
                            }
                            $image->setImageFormat('jpg');
                            $image->writeImage(IMAGES_PATH . 'posting/temp/temp' . $user->userId . '.jpg');

                            $response = $this->VK->getWallUploadServer($group->ownerId, $user->access_token);


                            if ($response['upload_url']) {
                                $filedata = IMAGES_PATH . 'posting/temp/temp' . $user->userId . '.jpg';

                                $json = $this->VK->uploadPhotoToServerVk($response['upload_url'], $filedata);

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

                if (count($attachments) && !count($string)) {
                    continue;
                }

                $text = $post->text;

                if ($group->hashtags) {
                    if ($group->hashtagsPos) {
                        $text = $group->hashtags . "\n" . $text;
                    } else {
                        $text .= "\n" . $group->hashtags;
                    }
                }
                $params = [
                    'owner_id' => '-' . $group->ownerId,
                    'from_group' => 1,
                    'message' => $text,
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
                    $post->isPostId = intval($response['post_id']);
                } elseif ($response['error'] == 5) {
                    $user->token_require = true;
                    $this->factoryUsers->users->save($user);
                }

                if ($this->factoryGrabber->posts->save($post)) {
                    $factoryMessages = new \Service\Messages\Model_Factory();
                    $mConfig = \Service\Messages\Model_Config::GetConfig();

                    $message = $factoryMessages->users->getNew();
                    $message->userId = $group->userId;
                    $message->isDone = false;
                    $message->type = \Service\Messages\Model_Config::TYPE_SYSTEM;
                    $text = $mConfig['grabber']['types']['publish']['text'];
                    $text = str_replace('%group_link%', $group->url, $text);
                    $message->text = $text;

                    $message->icon = 'grabber';
                    $factoryMessages->users->save($message);

                    $group->datePost = time();

                    if ($group->isFree) {
                        --$group->isFreeCount;
                    }
                    $this->factoryGrabber->groups->save($group);
                    $source = $this->factoryGrabber->sources->getById($post->sourceId, true);

                    if ($source !== null) {
                        ++$source->countAll;
                        $this->factoryGrabber->sources->save($source);
                    }
                }
                usleep(30000);
                break;
            }
        }
    }

    /*
     * Публикует сбрабленные посты в группе
     */
    public function A_Run()
    {

        //Установим время и дату для записи в лог скрипта
        $date = new DateTime();
        $date = $date->format("Y-m-d H:i:s");
        $tm = time();

        //Зададим переменные для отслеживаемых метрик скрипта
        $countActiveGroups = 0;
        $countPosts = 0;

        //выбераем активные граберы
        $query = $this->factoryGrabber->groups->query()->limit(500);
        $query->filter
            ->fieldValue('isActive', '=', true)
            //->fieldValue('userId', '=', \Config::$adminId) //для тестов!
            ->fieldValue('userActive', '=', true);

        $groups = $query->iteratorForSave();


        /** @var Model_Groups_Group $group */
        foreach ($groups as $group) {

            // Проверка демо доступа
            if ($group->isFree && $group->isFreeCount < 1) {
                continue;
            }

            $countActiveGroups++;

            // Проверка на соотвествие заданным интервалам публикации
            if ($group->datePost > time() - ($group->interval * 60)) {
                continue;
            }

            //Если в настройках заданы лимиты на публикацию - в заданное время
            if ($group->timeLimit) {

                $now = time();
                $from = mktime($group->timeHourFrom, $group->timeMinuteFrom, 0, date('m'), date('d'), date('Y'));
                $to = mktime($group->timeHourTo, $group->timeMinuteTo, 0, date('m'), date('d'), date('Y'));

                if ($to < $from) {
                    if (date('d') == date('d', $to)) {
                        $from -= 86400;
                    } else {
                        $to += 86400;
                    }
                }

                if ($now > $from && $now < $to) {
                    continue;
                }
            }

            //Если в настройках задано - публиковать рандомный пост
            if ($group->random) {
                $posts = $this->factoryGrabber->posts->getByGroupIdIsPost($group->groupId, false, true, 100);
                shuffle($posts);
                array_slice($posts, 0, 1);
            } else {
                $posts = $this->factoryGrabber->posts->getByGroupIdIsPost($group->groupId, false, true, 1);
            }

            $user = $this->factoryUsers->users->getById($group->userId, true);


            if ($user->token_require) {
                continue;
            }

            if (!$user->access_token) {
                continue;
            }


            //Публикуем пост

            foreach ($posts as $post) {


                $string = [];
                $attachments = $post->getAttachments();

                //Работа с вложениями
                foreach ($attachments as $attachment) {

                    switch ($attachment['type']) {
                        case 'video':
                            $string[] = 'video' . $attachment['video']['owner_id'] . '_' . $attachment['video']['id'];
                            break;
                        case 'doc':
                            $string[] = 'doc' . $attachment['doc']['owner_id'] . '_' . $attachment['doc']['id'];
                            break;
                        case 'link':
                            $string[] = $attachment['link']['url'];
                            break;
                        case 'poll':
                            $answers = [];

                            foreach ($attachment['poll']['answers'] as $answer) {
                                $answers[] = $answer['text'];
                            }

                            $response = $this->VK->pollsCreate($attachment['poll']['question'], $group->ownerId, 2, json_encode($answers, JSON_UNESCAPED_UNICODE), $user->access_token);


                            if ($response['id'] > 0) {
                                usleep(200000);
                                $string[] = 'poll' . $response['owner_id'] . '_' . $response['id'];
                            }
                            break;
                        case 'photo':
                            $photoUrl = '';

                            //Старый вариант
                            if (isset($attachment['photo']['photo_2560'])) {
                                $photoUrl = $attachment['photo']['photo_2560'];
                            } elseif (isset($attachment['photo']['photo_1280'])) {
                                $photoUrl = $attachment['photo']['photo_1280'];
                            } elseif (isset($attachment['photo']['photo_807'])) {
                                $photoUrl = $attachment['photo']['photo_807'];
                            } elseif (isset($attachment['photo']['photo_604'])) {
                                $photoUrl = $attachment['photo']['photo_604'];
                            } elseif (isset($attachment['photo']['photo_130'])) {
                                $photoUrl = $attachment['photo']['photo_130'];
                            }

                            //условие для нового API
                            if(!$photoUrl){
                                $photo = max($attachment['photo']['sizes']); //Выбираем изображение с максимальным разрешением
                                $photoUrl = $photo['url'];
                            }


                            try {
                                $data = file_get_contents($photoUrl);
                                $image = new Imagick();
                                $image->readImageBlob($data);
                            } catch (\Exception $e) {
                                continue 3;
                            }

                            if ($group->isWatermark & 4) {
                                $this->_getPhotoDataText($group, $image);
                            }

                            if ($group->isWatermark & 2) {
                                $this->_getPhotoDataWatermark($group, $image);
                            }
                            $image->setImageFormat('jpg');
                            $image->writeImage(IMAGES_PATH . 'posting/temp/temp' . $user->userId . '.jpg');

                            $response = $this->VK->getWallUploadServer($group->ownerId, $user->access_token);

                            if (!empty($response['upload_url'])) {

                                $filedata = IMAGES_PATH . 'posting/temp/temp' . $user->userId . '.jpg';
                                $json = $this->VK->uploadPhotoToServerVk($response['upload_url'], $filedata);

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



                if (count($attachments) && !count($string)) {
                    continue;
                }

                $text = $post->text;

                if ($group->hashtags) {
                    if ($group->hashtagsPos) {
                        $text = $group->hashtags . "\n" . $text;
                    } else {
                        $text .= "\n" . $group->hashtags;
                    }
                }

                $params = [
                    'owner_id' => '-' . $group->ownerId,
                    'from_group' => 1, //запись от имяни сообщества
                    'message' => $text,
                    'signed' => $post->signature ? 1 : 0, //подпись
                    'access_token' => $user->access_token,
                ];



                if (count($string)) {
                    $params['attachments'] = implode(',', $string);
                }

                if ($post->ads) {
                    $params['mark_as_ads'] = 1;
                }

                $response = $this->VK->addPost($params);

                if (!empty($response['post_id'])) {
                    $post->isPost = true;
                    $post->isPostDate = time();
                    $post->isPostId = intval($response['post_id']);
                } elseif (!empty($response['error']) and $response['error'] == 5) {
                    $user->token_require = true;
                    $this->factoryUsers->users->save($user);
                }

                if ($this->factoryGrabber->posts->save($post)) {
                    $countPosts++;
                    $factoryMessages = new \Service\Messages\Model_Factory();
                    $mConfig = \Service\Messages\Model_Config::GetConfig();

                    $message = $factoryMessages->users->getNew();
                    $message->userId = $group->userId;
                    $message->isDone = false;
                    $message->type = \Service\Messages\Model_Config::TYPE_SYSTEM;
                    $text = $mConfig['grabber']['types']['publish']['text'];
                    $text = str_replace('%group_link%', $group->url, $text);
                    $message->text = $text;

                    $message->icon = 'grabber';
                    $factoryMessages->users->save($message);

                    $group->datePost = time();

                    if ($group->isFree) {
                        --$group->isFreeCount;
                    }
                    $this->factoryGrabber->groups->save($group);
                    $source = $this->factoryGrabber->sources->getById($post->sourceId, true);

                    if ($source !== null) {
                        ++$source->countAll;
                        $this->factoryGrabber->sources->save($source);
                    }
                }
                usleep(30000);
                break;
            }
        }

        //Если есть опубликованные посты выведем для записи в лог скрипта
        if($countPosts){
            echo "\naction=Grabber/Grabber:Run ";
            echo $date;
            echo "\nВремя выполнения: " . round((time() - $tm) / 60, 2);
            echo "\nВсего групп с активированным грабером: " . $countActiveGroups++;;
            echo "\nВсего опубликовано постов: " . $countPosts;
            echo "\n";
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

        if (!is_file(IMAGES_PATH . 'grabber/watermark/' . $group->watermark)) {
            return;
        }
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

    public function A_getDomains()
    {
        $html = file_get_contents('https://www.reg.ru/domain/new/zonepedia/');

        $dom = new \DOMDocument();
        $dom->recover = true;
        $dom->formatOutput = false;
        $dom->strictErrorChecking = false;

        @$dom->loadHTML($html);
        $xpath = new \DOMXpath($dom);

        $path = '//*[@class="b-table-tlds__name no-tooltip l-margin_right-small"]';
        $elements = $xpath->query($path);

        foreach ($elements as $element) {
            $list[] = mb_strtolower($element->nodeValue);
        }

        Model_Config::setDomains($list);
    }

    /*
     * Обновляем активацию сервиса грабер
     */
    public function A_UpdateActive()
    {
        //mail('42-36-42@mail.ru','A_Run()', 'Старт Grabber:A_UpdateActive()');
        $query = $this->factoryGrabber->groups->query()->limit(100)->sort('groupId', 'ASC');
        $query->filter->fieldValue('isActive', '=', true)
            ->fieldValue('dateValid', '<', time());

        $it = $query->iteratorForSave();

        foreach ($it as $group) {
            $group->isActive = false;
            $this->factoryGrabber->groups->save($group);
        }

        $query = $this->factoryGrabber->groups->query()->limit(100)->sort('groupId', 'ASC');
        $query->filter->fieldValue('isActive', '=', false)
            ->fieldValue('dateValid', '>', time());

        $it = $query->iteratorForSave();

        foreach ($it as $group) {
            $group->isActive = true;
            $this->factoryGrabber->groups->save($group);
        }

        $page = 0;
        do {
            $count = 0;
            $query = $this->factoryGrabber->sources->query()->limit(1000)->offset(1000 * $page)->sort('sourceId',
                'ASC');
            $it = $query->iterator();
            /** @var Model_Sources_Source $source */
            foreach ($it as $source) {
                $count++;
                $source->makeShadow();
                $source->countAll = $this->factoryGrabber->posts->getCountsBySource($source->sourceId);
                $source->count = $source->countAll - $this->factoryGrabber->posts->getCountsBySource($source->sourceId,
                        true);
                $this->factoryGrabber->sources->save($source);
            }
            $page++;
        } while ($count);
    }

    /*
     * Грабит посты из источников
     */
    public function A_Grab()
    {

        //Установим время и дату для записи в лог скрипта
        $date = new DateTime();
        $date = $date->format("Y-m-d H:i:s");
        $tm = time();

        //Зададим переменные для отслеживаемых метрик скрипта
        $countActiveGroups = 0;
        $countPosts = 0;
        $countSources = 0;

        $page = 0;
        do {
            $count = 0;
            //Выбираем группы с активным граббером
            $query = $this->factoryGrabber->groups->query()->limit(100)->offset($page * 100)->sort('groupId', 'ASC');
            $query->filter
                ->fieldValue('isActive', '=', true)
                //->fieldValue('userId', '=', \Config::$adminId) //для тестов!
                ->fieldValue('userActive', '=', true);
            $groups = $query->iteratorForSave();


            /** @var \Service\Grabber\Model_Groups_Group $group */
            foreach ($groups as $group) {
                $count++;

                $countActiveGroups++;

                //Получаем пользователя которому принадлежит группа
                $user = $this->factoryUsers->users->getById($group->userId);

                if (!$user) {
                    continue;
                }


                //Выбираем источники для граббинга
                $sources = $this->factoryGrabber->sources->getByGroupId($group->groupId, true);

                foreach ($sources as $source) {

                    $countSources++;

                    $filter = 'all'; //забираем все посты

                    //настройки фильтров выборки постов
                    if ($source->notFromGroup) {
                        $filter = 'others';
                    } elseif ($source->withFromGroup) {
                        $filter = 'owner';
                    }


                    //Ограничение по количеству собираемых постов
                    // $source->count - скольк уже взяли постов из данного источника
                    // $group->maxLength - сколько задано в настройках граббера
                    if ($source->count >= $group->maxLength) {
                        continue;
                    }

                    //Получаем список запесей со стены сообщества

                    $response = $this->VK->getPosts($source->ownerId, 2,$user->access_token ?: $this->settings['service'], 10);
                    if(empty($response['items'])) continue;

                    foreach ($response['items'] as $data) {


                        if (!isset($data['attachments'])) {
                            $data['attachments'] = [];
                        }


                        if (!$this->checkPost($data, $source)) {
                            continue;
                        }


                        //Если в настройках задано - удалять все тексты
                        if ($source->delText) {
                            $data['text'] = '';
                        } else {

                            // Если в настройках задано удалять хэштеги
                            if ($source->delHashtags) {
                                $data['text'] = $this->delHashtags($data['text']);
                            }

                            // Если в настройках задано - удалять все ссылки
                            if ($source->delLinks) {
                                $urls = $this->getUrls($data['text']);

                                foreach ($urls as $url) {
                                    $data['text'] = str_replace($url, '', $data['text']);
                                }
                            }

                            // Если в настройках задано - удалять все ссылки VK
                            if ($source->delVKLinks) {
                                $vkUrls = $this->_getVkUrls($data['text']);

                                foreach ($vkUrls as $url) {
                                    $data['text'] = str_replace($url, '', $data['text']);
                                }
                            }

                            // Если в настройках задано - удалять все Emoji
                            if ($source->delEmoji) {
                                $emoji = \Service\Posting\Model_Config::GetEmoji();

                                foreach ($emoji as $egroup) {
                                    foreach ($egroup['codes'] as $text) {
                                        $data['text'] = str_replace($text, '', $data['text']);
                                    }
                                }
                            }
                        }

                        //Создаем пост для записи в БД
                        $post = $this->factoryGrabber->posts->getNew();
                        $post->sourceId = $source->sourceId;
                        $post->text = trim($data['text']);


                        // Фильтр по вложениям
                        $postAttachments = [];
                        foreach ($data['attachments'] as $attachment) {

                            if (in_array($attachment['type'],
                                ['market', 'market_album', 'wall', 'wall_reply', 'sticker', 'gift'])) {
                                continue;
                            }

                            if ($source->delVideo && $attachment['type'] == 'video') {
                                continue;
                            }

                            if ($source->delPoll && $attachment['type'] == 'poll') {
                                continue;
                            }

                            if ($source->linkDelete && $attachment['type'] == 'link') {
                                continue;
                            }

                            $postAttachments[] = $attachment;
                        }


                        if (!$post->text && !count($postAttachments)) {
                            continue;
                        }

                        $post->setAttachments($postAttachments);
                        $post->userId = $source->userId;
                        $post->groupId = $source->groupId;
                        $post->dateCreate = time();
                        $post->sourceItemId = intval($data['id']);
                        $post->isPost = false;
                        $post->ads = false;


                        // Сохраняем сграбленный пост
                        if ($this->factoryGrabber->posts->save($post)) {
                            $countPosts++;
                            // И прибавляем $source->count - взяли еще один пост из данного источника
                            ++$source->count;
                            $this->factoryGrabber->sources->save($source);
                        }
                    }
                    usleep(200);
                }
                sleep(1);
            }
            $page++;
        } while ($count);


        //Если есть сграбленные посты выведем для записи в лог скрипта
        if($countPosts){
            echo "\naction=Grabber/Grabber:Grab ";
            echo $date;
            echo "\nВремя выполнения: " . round((time() - $tm) / 60, 2);
            echo "\nВсего групп с активированным грабером: " . $countActiveGroups;
            echo "\nВсего источников: " . $countSources;
            echo "\nВсего сграблено постов: " . $countPosts;
            echo "\n";
        }

    }

    /*
     * Проверяет пост на соответствие фильрам
     *  - вернет false если пост данного вида запрещен настройками граббера
     */
    private function checkPost($data, Model_Sources_Source $source)
    {

        $urls = $this->getUrls($data['text']);
        $vkUrls = $this->_getVkUrls($data['text']);

        if ($data['marked_as_ads'] == 1 && $source->notAdv === true) {
            return false;
        }


        //Если установлен фильтр для источника
        if ($source->filter != '') {
            $found = false;
            $filters = explode(',', $source->filter);

            foreach ($filters as $filter) {

                if(!$filter) continue;

                $filter = mb_strtolower(trim($filter));

                if (strpos(mb_strtolower($data['text']), $filter) !== false) {
                    $found = true;
                }
            }


            if (!$found) {
                return false;
            }
        }



        //Если установлен blacklist для источника
        if ($source->blacklist != '') {
            $found = false;
            $words = explode(',', $source->blacklist);

            foreach ($words as $word) {
                $word = mb_strtolower(trim($word));

                if (strpos(mb_strtolower($data['text']), $word) !== false) {
                    $found = true;
                }
            }

            if ($found) {
                return false;
            }
        }

        if ($source->notLink && count($urls)) {
            return false;
        }

        if ($source->notVKLink && count($vkUrls)) {
            return false;
        }

        if (isset($data['is_pinned']) && $data['is_pinned'] == 1 && $source->notFixed) {
            return false;
        }

        if ($source->notTextOnly && !(isset($data['attachments']) && count($data['attachments']) > 0)) {
            return false;
        }



        if ($source->notPhotoOnly && !$data['text']) {
            $found = true;

            foreach ($data['attachments'] as $attachment) {
                if ($attachment['type'] != 'photo') {
                    $found = false;
                }
            }

            if ($found) {
                return false;
            }
        }

        if ($source->notPhoto) {
            $found = false;

            foreach ($data['attachments'] as $attachment) {
                if ($attachment['type'] == 'photo') {
                    $found = true;
                }
            }

            if ($found) {
                return false;
            }
        }

        if ($source->notVideo) {
            $found = false;

            foreach ($data['attachments'] as $attachment) {
                if ($attachment['type'] == 'video') {
                    $found = true;
                }
            }

            if ($found) {
                return false;
            }
        }

        if ($source->notMusic || true) {
            $found = false;

            foreach ($data['attachments'] as $attachment) {
                if ($attachment['type'] == 'audio') {
                    $found = true;
                }
            }

            if ($found) {
                return false;
            }
        }


        if ($source->notDoc) {
            $found = false;

            foreach ($data['attachments'] as $attachment) {
                if ($attachment['type'] == 'doc') {
                    $found = true;
                }
            }

            if ($found) {
                return false;
            }
        }

        if ($source->notGif) {
            $found = false;

            foreach ($data['attachments'] as $attachment) {
                if ($attachment['type'] == 'doc' && $attachment['doc']['ext'] == 'gif') {
                    $found = true;
                }
            }

            if ($found) {
                return false;
            }
        }

        if ($source->withPhoto) {
            $found = false;

            foreach ($data['attachments'] as $attachment) {
                if ($attachment['type'] == 'photo') {
                    $found = true;
                }
            }

            if (!$found) {
                return false;
            }
        }

        if ($source->withVideo) {
            $found = false;

            foreach ($data['attachments'] as $attachment) {
                if ($attachment['type'] == 'video') {
                    $found = true;
                }
            }

            if (!$found) {
                return false;
            }
        }


        if ($source->withDoc) {
            $found = false;

            foreach ($data['attachments'] as $attachment) {
                if ($attachment['type'] == 'doc') {
                    $found = true;
                }
            }

            if (!$found) {
                return false;
            }
        }



        if ($source->withGif) {
            $found = false;

            foreach ($data['attachments'] as $attachment) {
                if ($attachment['type'] == 'doc' && $attachment['doc']['ext'] == 'gif') {
                    $found = true;
                }
            }

            if (!$found) {
                return false;
            }
        }


        //Проверка. Если такой пост был уже опубликован вернем false
        $query = $this->factoryGrabber->posts->query()->sqlCalcFoundRows(true);
        $query->filter
            ->fieldValue('sourceId', '=', $source->sourceId)
            ->fieldValue('sourceItemId', '=', intval($data['id']));
        $it = $query->iterator();

        if ($it->getTotal()) {
            return false;
        }

        return true;
    }

    private function getUrls($text)
    {
        $urls = [];

        $domains = Model_Config::getDomains();

        $arr = explode('<br />', $text);

        foreach ($arr as $string) {
            $arr1 = explode(' ', $string);

            foreach ($arr1 as $string1) {
                $arr2 = explode("\n", $string1);

                foreach ($arr2 as $string2) {
                    if (strpos($string2, '.') === false) {
                        continue;
                    }
                    $found = false;

                    foreach ($domains as $domain) {
                        if (strpos($string2, $domain)) {
                            $found = true;
                        }
                    }

                    if (!$found) {
                        continue;
                    }

                    $urls[] = $string2;
                }
            }
        }

        return $urls;
    }

    private function _getVkUrls($text)
    {
        preg_match_all('/(\[[^\[\]]+\|[^\[\]]+\])/i', $text, $matches);

        return $matches[0] ?? [];
    }

    public function delHashtags($text)
    {
        $pattern = '/(#[0-9a-zA-Zа-яёА-ЯЁ]+)/u';

        $text = preg_replace($pattern, '', $text);

        return $text;
    }
}
