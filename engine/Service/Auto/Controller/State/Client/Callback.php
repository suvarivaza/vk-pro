<?php

namespace Service\Auto;

use Imagick;
use Lib_Text;
use Lib_Uuid;

class Controller_State_Client_Callback extends Controller_State_Client
{
    private $_attachmentTypes = [
        'audio' => 2,
        'video' => 4,
        'doc' => 8,
        'photo' => 16,
        'poll' => 32,
        'text' => 64,
    ];

    private $prices = [];
    private $percents = [];
    private $percentsVals = [];

    public function actionPrepare()
    {

        return null;
    }

    public function actionGet()
    {


        $entityBody = file_get_contents('php://input');
        $json = json_decode($entityBody, true);



        if ($json['type'] == 'confirmation') {
            $query = $this->factoryAuto->auto->groups->query();
            $query->filter->fieldValue('ownerId', '=', intval($json['group_id']));
            $it = $query->iterator();
            $group = $it->current();

            if ($group) {
                echo $this->_response->setBody($group->code);
                exit;
            }

//            logMail('Vk-Pro.top АПИ', '
//                    engine/Service/Auto/Controller/State/Client/Callback.php
//                    Response: ' . print_r($json, true));
        }

        if ($json['type'] == 'wall_post_new') {
            $this->prices = [
                'likes' => floatval($this->_application->settings['price_likes_buy']),
                'reposts' => floatval($this->_application->settings['price_reposts_buy']),
                'comments' => floatval($this->_application->settings['price_comments_buy']),
                'join' => floatval($this->_application->settings['price_join_buy']),
                'polls' => floatval($this->_application->settings['price_polls_buy']),
                'views' => floatval($this->_application->settings['price_views_buy']),
                'video' => floatval($this->_application->settings['price_video_buy']),
            ];
            $this->percents = [
                'percent_sex' => floatval($this->_application->settings['percent_sex']),
                'percent_ageFrom' => floatval($this->_application->settings['percent_ageFrom']),
                'percent_ageTo' => floatval($this->_application->settings['percent_ageTo']),
                'percent_country' => floatval($this->_application->settings['percent_country']),
                'percent_city' => floatval($this->_application->settings['percent_city']),
                'percent_city_my' => floatval($this->_application->settings['percent_city_my']),
                'percent_relation' => floatval($this->_application->settings['percent_relation']),
                'percent_avatarCount' => floatval($this->_application->settings['percent_avatarCount']),
                'percent_filled' => floatval($this->_application->settings['percent_filled']),
                'percent_pageAge' => floatval($this->_application->settings['percent_pageAge']),
                'percent_followersCount' => floatval($this->_application->settings['percent_followersCount']),
                'percent_interestingPage' => floatval($this->_application->settings['percent_interestingPage']),
                'percent_frequencyPost' => floatval($this->_application->settings['percent_frequencyPost']),
                'percent_prior' => floatval($this->_application->settings['percent_prior']),
            ];
            $this->percentsVals = json_decode(file_get_contents(ENGINE_PATH . 'engine/Service/Tasks/Model/Config.json'),
                true);

            $this->_templatesCheck($json['object']);
        }

        if ($json['type'] == 'wall_repost') {
            $this->prices = [
                'likes' => floatval($this->_application->settings['price_likes_buy']),
                'reposts' => floatval($this->_application->settings['price_reposts_buy']),
                'comments' => floatval($this->_application->settings['price_comments_buy']),
                'join' => floatval($this->_application->settings['price_join_buy']),
                'polls' => floatval($this->_application->settings['price_polls_buy']),
                'views' => floatval($this->_application->settings['price_views_buy']),
                'video' => floatval($this->_application->settings['price_video_buy']),
            ];
            $this->percents = [
                'percent_sex' => floatval($this->_application->settings['percent_sex']),
                'percent_ageFrom' => floatval($this->_application->settings['percent_ageFrom']),
                'percent_ageTo' => floatval($this->_application->settings['percent_ageTo']),
                'percent_country' => floatval($this->_application->settings['percent_country']),
                'percent_city' => floatval($this->_application->settings['percent_city']),
                'percent_city_my' => floatval($this->_application->settings['percent_city_my']),
                'percent_relation' => floatval($this->_application->settings['percent_relation']),
                'percent_avatarCount' => floatval($this->_application->settings['percent_avatarCount']),
                'percent_filled' => floatval($this->_application->settings['percent_filled']),
                'percent_pageAge' => floatval($this->_application->settings['percent_pageAge']),
                'percent_followersCount' => floatval($this->_application->settings['percent_followersCount']),
                'percent_interestingPage' => floatval($this->_application->settings['percent_interestingPage']),
                'percent_frequencyPost' => floatval($this->_application->settings['percent_frequencyPost']),
                'percent_prior' => floatval($this->_application->settings['percent_prior']),
            ];
            $this->percentsVals = json_decode(file_get_contents(ENGINE_PATH . 'engine/Service/Tasks/Model/Config.json'),
                true);

            $this->_templatesCheck($json['object']);
        }

        echo $this->_response->setBody('ok');
        exit;
    }

    protected function _templatesCheck($data)
    {
        if (isset($data['copy_history'])) {
            $data['text'] = $data['copy_history'][0]['text'];
            $data['attachments'] = $data['copy_history'][0]['attachments'];
        }

        if (isset($data['post_type']) && $data['post_type'] == 'suggest') {
            return true;
        }

        $ready = [];

        if (!isset($data['attachments'])) {
            $data['attachments'] = [];
        }
        $query = $this->factoryAuto->auto->groups->query();

        $query->filter->fieldValue('ownerId', '=', abs($data['owner_id']));
        $it = $query->iterator();
        /** @var Model_Autos_Groups_Group $group */
        $group = $it->current();

        if (!$group) {
            echo $this->_response->setBody('ok');
            exit;
        }
        $group->makeShadow();
        $templates = $this->factoryAuto->auto->templates->getByGroupId($group->autoGroupId, true);
        $beginOfDay = strtotime('midnight');

        foreach ($templates as $template) {
            if (isset($ready[$template->type])) {
                continue;
            }

            if ($GLOBALS['isSuperUser']) {
                file_put_contents(ENGINE_PATH . 'result.txt', print_r($data, true));
            }

            if (!$template->isActive) {
                continue;
            }

            if ($template->isArchive) {
                continue;
            }

            $post = $this->factoryAuto->auto->templates->posts->getByTemplateIdItemId($template->templateId,
                $data['id']);

            if ($post !== null) {
                continue;
            }

            $post = $this->factoryAuto->auto->templates->posts->getNew();
            $post->templateId = $template->templateId;
            $post->itemId = intval($data['id']);

            if ($template->weekDay > 0 && $template->weekDay < 8 && $template->weekDay != date('N')) {
                continue;
            }

            if ($template->weekDay == 8) {
                if ($beginOfDay > $template->weekDate) {
                    $template->isActive = false;
                    $this->factoryAuto->auto->templates->save($template);

                    continue;
                } elseif ($beginOfDay < $template->weekDate) {
                    continue;
                }
            }

            if ($template->hourFrom > 0 && $template->hourFrom > date('G')) {
                continue;
            }

            if ($template->hourTo > 0 && $template->hourTo < date('G')) {
                continue;
            }

            if ($template->postId == $data['id']) {
                continue;
            }

            $attachmentType = 0;

            foreach ($data['attachments'] as $attachment) {
                $attachmentType |= $this->_attachmentTypes[$attachment['type']];
            }

            if ($data['text']) {
                $attachmentType |= $this->_attachmentTypes['text'];
            }

            if (!($attachmentType & $template->attachmentType)) {
                continue;
            }

            if ($template->adsOut && $data['marked_as_ads']) {
                continue;
            }

            if ($template->fromGroupOnly && $data['from_id'] != $data['owner_id']) {
                continue;
            }

            $template->postId = intval($data['id']);

            if ($this->factoryAuto->auto->templates->save($template)) {
                $ready[$template->type] = true;
                $this->factoryAuto->auto->templates->posts->save($post);

                if ($this->createTask($template, $data, $group)) {
                    if ($group->isFree) {
                        if ($group->isFreeCount > 1) {
                            --$group->isFreeCount;
                        } else {
                            $group->isFreeCount = 0;
                        }
                        $this->factoryAuto->auto->groups->save($group);
                    }
                }
            }
        }

        return $this->_response->setBody('ok');
    }

    protected function createTask(Model_Autos_Templates_Template $template, $post, Model_Autos_Groups_Group $group)
    {
        $user = $this->factoryUsers->users->getById($template->userId, true);

        $count = rand($template->countFrom, $template->countTo);

        if ($count < 1) {
            $count = $template->countTo;
        }

        if (!$count) {
            return false;
        }
        $task = $this->factoryTasks->tasks->getNew();
        $poll = null;
        $video = null;

        foreach ($post['attachments'] as $attachment) {
            if ($attachment['type'] == 'poll') {
                $poll = $attachment['poll'];
                break;
            }

            if ($attachment['type'] == 'video') {
                $video = $attachment;
            }
        }

        $task->type = $template->type;
        $task->minKarma = $template->minKarma;
        $task->followersOnly = false;
        $task->newFollowers = false;
        $task->prior = $template->prior;
        $task->count = $count;
        $task->countReady = 0;
        $task->countRemain = $task->count - $task->countReady;
        $task->userId = $template->userId;
        $task->dateCreate = time();
        $task->url = 'https://vk.com/wall' . $post['owner_id'] . '_' . $post['id'];
        $task->targeting = $template->targeting;
        $task->isTemplate = true;
        $task->templateId = $template->templateId;

        if ($task->type == 'polls') {
            if (!$poll) {
                return false;
            }
            $task->pollId = $poll['id'];
            $task->answerId = 0;
            $task->answerTitle = 'Любой вариант';
            $answerIds = [];

            foreach ($poll['answers'] as $answer) {
                $answerIds[] = $answer['id'];
            }

            if (!count($answerIds)) {
                return false;
            }

            $task->answerIds = implode(',', $answerIds);
        }

        if ($task->type == 'video') {
            if (!$video) {
                return false;
            }
            $task->url = 'https://vk.com/video' . $video['video']['owner_id'] . '_' . $video['video']['id'];
        }

        if ($task->targeting) {
            $task->sex = $template->sex;
            $task->ageFrom = $template->ageFrom;
            $task->ageTo = $template->ageTo;
            $task->cityId = $template->cityId;
            $task->countryId = $template->countryId;
            $task->relation = $template->relation;
            $task->avatarCount = $template->avatarCount;
            $task->filled = $template->filled;
            $task->pageAge = $template->pageAge;
            $task->followersCount = $template->followersCount;
            $task->interestingPage = $template->interestingPage;
            $task->frequencyPost = $template->frequencyPost;
        }

        $task->commentType = $template->commentType;
        $task->setComments($template->getComments());
        $task->vkType = 'post';
        $task->ownerId = strval($post['owner_id']);
        $task->itemId = strval($post['id']);

        foreach ($post['attachments'] as $attachment) {
            if ($attachment['type'] == 'photo') {
                $photo = [
                    'small' => $this->savePhoto($attachment['photo']['photo_75']),
                    'big' => $this->savePhoto($attachment['photo']['photo_130']),
                ];
                $task->setPhoto($photo);
                break;
            }
        }
        $task->title = Lib_Text::Truncate(strip_tags($post['text']));

        $photo = $task->getPhoto();

        if (!isset($photo['small'])) {
            if (intval($task->ownerId) > 0) {

                $result = $this->VK->getUser($task->ownerId, $user->access_token);

                $photo = [
                    'small' => $this->savePhoto($result[0]['photo_50']),
                    'big' => $this->savePhoto($result[0]['photo_200']),
                ];
                $task->setPhoto($photo);
            } else {

                $result = $this->VK->getGroup($task->ownerId, $user->access_token);

                if (isset($result[0]['photo_100']) && $result[0]['photo_100'] != '') {
                    $photo = [
                        'small' => $this->savePhoto($result[0]['photo_100']),
                        'big' => $this->savePhoto($result[0]['photo_100']),
                    ];
                    $task->setPhoto($photo);
                }
            }
        }

        if ($template->specialId > 0) {
            $task->minKarma = 50;
            $task->isSpecial = true;
            $task->specialId = $template->specialId;
        }

        $price = $this->prices[$task->type] * $task->count;
        $sum = $price;

        if ($task->sex > 0) {
            $val = $this->percentsVals['sex'][$task->sex] ?: $this->percents['percent_sex'];
            $sum += $price * $val / 100;
        }

        if ($task->ageFrom > 0) {
            $val = $this->percentsVals['ageFrom'][$task->ageFrom] ?: $this->percents['percent_ageFrom'];
            $sum += $price * $val / 100;
        }

        if ($task->ageTo > 0) {
            $val = $this->percentsVals['ageTo'][$task->ageTo] ?: $this->percents['percent_ageTo'];
            $sum += $price * $val / 100;
        }

        if ($task->prior && $this->percents['percent_prior'] > 0) {
            $sum += $price * ($this->percents['percent_prior'] / 100);
        }

        if ($task->cityId == $user->cityId) {
            $sum += $price * $this->percents['percent_city_my'] / 100;
        } elseif ($task->cityId > 0) {
            $sum += $price * $this->percents['percent_city'] / 100;
        } elseif ($task->countryId > 0) {
            $sum += $price * $this->percents['percent_country'] / 100;
        }

        if ($task->relation > 0) {
            $val = $this->percentsVals['relation'][$task->relation] ?: $this->percents['percent_relation'];
            $sum += $price * $val / 100;
        }

        if ($task->minKarma > 0) {
            $val = $this->percentsVals['minKarma'][$task->minKarma] ?: 0;
            $sum += $price * $val / 100;
        }

        if ($task->avatarCount > 0) {
            $val = $this->percentsVals['avatarCount'][$task->avatarCount] ?: $this->percents['percent_avatarCount'];
            $sum += $price * $val / 100;
        }

        if ($task->filled > 0) {
            $val = $this->percentsVals['filled'][$task->filled] ?: $this->percents['percent_filled'];
            $sum += $price * $val / 100;
        }

        if ($task->pageAge > 0) {
            $val = $this->percentsVals['pageAge'][$task->pageAge] ?: $this->percents['percent_pageAge'];
            $sum += $price * $val / 100;
        }

        if ($task->followersCount > 0) {
            $val = $this->percentsVals['followersCount'][$task->followersCount] ?: $this->percents['percent_followersCount'];
            $sum += $price * $val / 100;
        }

        if ($task->interestingPage > 0) {
            $val = $this->percentsVals['interestingPage'][$task->interestingPage] ?: $this->percents['percent_interestingPage'];
            $sum += $price * $val / 100;
        }

        if ($task->frequencyPost > 0) {
            $val = $this->percentsVals['frequencyPost'][$task->frequencyPost] ?: $this->percents['percent_frequencyPost'];
            $sum += $price * $val / 100;
        }

        $task->price = $sum / $task->count;
        $task->sum = floatval($sum);

        if ($group->dateValid < time() || ($group->isFree && $group->isFreeCount < 1)) {
            $factoryMessages = new \Service\Messages\Model_Factory();

            $message = $factoryMessages->users->getNew();
            $message->userId = $task->userId;
            $message->isDone = false;
            $message->type = \Service\Messages\Model_Config::TYPE_SYSTEM;
            $text = 'Срок действия автоведения для группы' . $group->title . ' истек. Создание задания %title% по шаблону %template% невозможно. Продлите срок действия автоведения для данной группы.';
            $text = str_replace('%title%', $task->title, $text);
            $text = str_replace('%template%', $template->title, $text);
            $message->text = $text;

            $message->icon = 'auto';
            $factoryMessages->users->save($message);

            $template->postId = intval($task->itemId);
            $this->factoryAuto->auto->templates->save($template);

            return false;
        }

        if (!$task->age_limits && intval($task->ownerId) < 0) {
            usleep(100000);

            $result = $this->VK->getGroup($task->ownerId, $user->access_token);

            $task->age_limits = intval($result[0]['age_limits']);
        }

        if ($task->sum < $user->balance && $template->balanceLimit == 0) {
            if ($this->factoryTasks->tasks->save($task)) {
                $factoryMessages = new \Service\Messages\Model_Factory();
                $mConfig = \Service\Messages\Model_Config::GetConfig();

                $message = $factoryMessages->users->getNew();
                $message->userId = $task->userId;
                $message->isDone = false;
                $message->type = \Service\Messages\Model_Config::TYPE_SYSTEM;
                $text = $mConfig['auto']['types']['add']['text'];
                $text = str_replace('%title%', $task->title, $text);
                $text = str_replace('%template%', $template->title, $text);
                $message->text = $text;

                $message->icon = 'auto';
                $factoryMessages->users->save($message);

                $template->postId = intval($task->itemId);
                $template->balanceRemain += $task->sum;

                if ($this->factoryAuto->auto->templates->save($template)) {
                    $balance = $this->factoryUsers->users->balance->getNew();
                    $balance->userId = $user->userId;
                    $balance->isTask = true;
                    $balance->balance = -$task->sum;
                    $balance->balanceFrom = $user->balance;
                    $user->balance -= $task->sum;
                    $balance->balanceTo = $user->balance;
                    $balance->dateCreate = time();
                    $balance->comment = 'Создание задания ' . $task->title;

                    if ($this->factoryUsers->users->save($user)) {
                        $this->factoryUsers->users->balance->save($balance);

                        return true;
                    }
                }
            }
        } elseif ($task->sum < ($template->balanceLimit - $template->balanceRemain) && $task->sum < $user->balance) {
            if ($this->factoryTasks->tasks->save($task)) {
                $factoryMessages = new \Service\Messages\Model_Factory();
                $mConfig = \Service\Messages\Model_Config::GetConfig();

                $message = $factoryMessages->users->getNew();
                $message->userId = $task->userId;
                $message->isDone = false;
                $message->type = \Service\Messages\Model_Config::TYPE_SYSTEM;
                $text = $mConfig['auto']['types']['add']['text'];
                $text = str_replace('%title%', $task->title, $text);
                $text = str_replace('%template%', $template->title, $text);
                $message->text = $text;

                $message->icon = 'auto';
                $factoryMessages->users->save($message);

                $template->postId = intval($task->itemId);
                $template->balanceRemain += $task->sum;

                if ($this->factoryAuto->auto->templates->save($template)) {
                    $balance = $this->factoryUsers->users->balance->getNew();
                    $balance->userId = $user->userId;
                    $balance->isTask = true;
                    $balance->balance = -$task->sum;
                    $balance->balanceFrom = $user->balance;
                    $user->balance -= $task->sum;
                    $balance->balanceTo = $user->balance;
                    $balance->dateCreate = time();
                    $balance->comment = 'Создание задания ' . $task->title;

                    if ($this->factoryUsers->users->save($user)) {
                        $this->factoryUsers->users->balance->save($balance);

                        return true;
                    }
                }
            }
        } elseif ($task->sum > $user->balance) {
            $factoryMessages = new \Service\Messages\Model_Factory();
            $mConfig = \Service\Messages\Model_Config::GetConfig();

            $message = $factoryMessages->users->getNew();
            $message->userId = $task->userId;
            $message->isDone = false;
            $message->type = \Service\Messages\Model_Config::TYPE_SYSTEM;
            $text = $mConfig['auto']['types']['balance']['text'];
            $message->text = $text;

            $message->icon = 'auto';
            $factoryMessages->users->save($message);

            return false;
        }

        return false;
    }

    protected function savePhoto($url)
    {
        $temp = explode('?', $url);
        $arr = explode('.', $temp[0]);
        $ext = array_pop($arr);

        $data = file_get_contents($url);
        $image = new Imagick();
        $image->readImageBlob($data);

        $path = 'tasks/' . rand(10, 99) . '/' . rand(10, 99) . '/' . rand(10, 99) . '/';
        $file = mb_substr(md5(Lib_Uuid::getNext()), 0, 11) . '.' . $ext;

        if (!is_dir(IMAGES_PATH . $path)) {
            mkdir(IMAGES_PATH . $path, 0777, true);
        }

        $image->writeImage(IMAGES_PATH . $path . $file);
        $photo = [
            'path' => $path,
            'file' => $file,
            'url' => '/img/' . $path . $file,
            'w' => $image->getImageWidth(),
            'h' => $image->getImageHeight(),
        ];

        return $photo;
    }

    public function actionPost()
    {
        return null;
    }
}
