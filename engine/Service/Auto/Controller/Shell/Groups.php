<?php

namespace Service\Auto;

use DateTime;
use Imagick;
use Lib_Text;
use Lib_Uuid;


class Controller_Shell_Groups extends Controller_Shell
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

    public function __construct()
    {
        parent::__construct();
    }


    public function A_Test()
    {

        $user = $this->factoryUsers->users->getById(\Config::$adminId);

        $response = file_get_contents('https://api.vk.com/method/groups.setCallbackSettings?group_id=106009940&wall_new=0&wall_repost=0&v=5.658&access_token=' . $user->access_token);

        dd($response);

    }

    public function A_Run()
    {


        $tm = time();
        $date = new DateTime();
        $date = $date->format("Y-m-d H:i:s");
        echo "\naction=Auto/Groups:Run ";
        echo $date;

        $factory = new \Service\System\Model_Factory();
        $it = $factory->settings->getAll();
        $settings = [];

        foreach ($it as $setting) {
            $settings[$setting->name] = $setting->value;
        }

        $this->prices = [
            'likes' => floatval($settings['price_likes_buy']),
            'reposts' => floatval($settings['price_reposts_buy']),
            'comments' => floatval($settings['price_comments_buy']),
            'join' => floatval($settings['price_join_buy']),
            'polls' => floatval($settings['price_polls_buy']),
            'views' => floatval($settings['price_views_buy']),
            'video' => floatval($settings['price_video_buy']),
        ];
        $this->percents = [
            'percent_sex' => floatval($settings['percent_sex']),
            'percent_ageFrom' => floatval($settings['percent_ageFrom']),
            'percent_ageTo' => floatval($settings['percent_ageTo']),
            'percent_country' => floatval($settings['percent_country']),
            'percent_city' => floatval($settings['percent_city']),
            'percent_city_my' => floatval($settings['percent_city_my']),
            'percent_relation' => floatval($settings['percent_relation']),
            'percent_avatarCount' => floatval($settings['percent_avatarCount']),
            'percent_filled' => floatval($settings['percent_filled']),
            'percent_pageAge' => floatval($settings['percent_pageAge']),
            'percent_followersCount' => floatval($settings['percent_followersCount']),
            'percent_interestingPage' => floatval($settings['percent_interestingPage']),
            'percent_frequencyPost' => floatval($settings['percent_frequencyPost']),
            'percent_prior' => floatval($settings['percent_prior']),
        ];
        $this->percentsVals = json_decode(file_get_contents(ENGINE_PATH . 'engine/Service/Tasks/Model/Config.json'),
            true);

        $query = $this->factoryAuto->auto->templates->query();
        $query->filter->fieldValue('isActive', '=', true);
        //$query->filter->fieldValue('userId', '=', \Config::$adminId); //Для тестов
        $query->filter->aggregatorOpen('OR')
            ->fieldValue('weekDay', '=', intval(date('N'))) //если порядковый номер указанного дня недели совпадает с текущим
            ->fieldValue('weekDay', '=', 0) //если не задано
            ->fieldValue('weekDay', '=', 9) //если указано - собирать посты постояннно
            ->aggregatorClose();
        $query->filter->aggregatorOpen('OR')
            ->fieldValue('hourFrom', '<', intval(date('G'))) //если заданное время меньше текущего
            ->fieldValue('hourFrom', '=', 0)
            ->aggregatorClose();
        $query->filter->aggregatorOpen('OR')
            ->fieldValue('hourTo', '<', intval(date('G')))
            ->fieldValue('hourTo', '=', 0)
            ->aggregatorClose();
        $it = $query->iteratorForSave();

        /** @var Model_Autos_Templates_Template $template */
        foreach ($it as $template) {
            $list[$template->groupId][] = $template;
        }

        $countCreateTasks = 0;
        $countTemplates = 0;
        $countGroups = count($list);
        foreach ($list as $groupId => $templates) {

            $group = $this->factoryAuto->auto->groups->getById($groupId);

            if ($group === null) continue;

            $user = $this->factoryUsers->users->getById($group->userId);

            if ($user === null) continue;

            $response = $this->VK->getPosts($group->ownerId, 2,$user->access_token);
            if(empty($response['items'])) continue;

            $countTemplates += count($templates);
            foreach ($templates as $template) {

                $post = null;

                if($template->weekDay == 9) $template->weekDay = 0;

                foreach ($response['items'] as $data) {

                    if ($data['date'] < strtotime('midnight')) {
                        break;
                    }

                    if ($template->postId == $data['id']) {
                        break;
                    }


                    if ($template->weekDay > 0 && $template->weekDay != date('N', $data['date'])) {
                        continue;
                    }

                    if ($template->hourFrom > 0 && $template->hourFrom > date('G', $data['date'])) {
                        continue;
                    }

                    if ($template->hourTo > 0 && $template->hourTo < date('G', $data['date'])) {
                        continue;
                    }

                    $attachmentType = 0;

                    foreach ($data['attachments'] as $attachment) {
                        if($attachment['type'] == 'link') continue; //PHP Notice: Undefined index: link in /var/www/www-root/data/www/vk-pro.top/engine/Service/Auto/Controller/Shell/Groups.php on line 168
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

                    $post = $data;
                    break;
                }

                if ($post) {
                    if($this->createTask($template, $post))
                    $countCreateTasks++;
                }
            }
        }

        echo "\nГрупп с автоведением: " . $countGroups;
        echo "\nВсего шаблонов: " . $countTemplates;
        echo "\nСоздано заданий: " . $countCreateTasks;
        echo "\nВремя выполнения скрипта: " . round((time() - $tm) / 60, 2);
        echo "\n";
    }

    protected function createTask(Model_Autos_Templates_Template $template, $post)
    {

        $user = $this->factoryUsers->users->getById($template->userId, true);
        $count = rand($template->countFrom, $template->countTo);
        $task = $this->factoryTasks->tasks->getNew();

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
        $task->ownerType = 2; //тип - группа
        $task->reason = '';

        $result = $this->VK->getPost($task->ownerId, $task->itemId, 2, $user->access_token);

        if (!empty($result['error'])) {
            $this->_errors[] = 'Не удалось определить пост';
        }

        foreach ($result[0]['attachments'] as $attachment) {
            if ($attachment['type'] == 'photo') {
                $photo = [
                    'small' => $this->savePhoto($attachment['photo']['sizes'][0]['url']),
                    'big' => $this->savePhoto($attachment['photo']['sizes'][2]['url']),
                ];
                $task->setPhoto($photo);
                break;
            }
        }

        $task->title = Lib_Text::Truncate(strip_tags($result[0]['text']));

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

        if ($task->sum < ($template->balanceLimit - $template->balanceRemain) && $task->sum < $user->balance) {
            if ($this->factoryTasks->tasks->save($task)) {
                $template->postId = intval($task->itemId);
                $template->balanceRemain += $task->sum;

                if ($this->factoryAuto->auto->templates->save($template)) {
                    $user->balance -= $task->sum;
                    $this->factoryUsers->users->save($user);
                }
            }
        } elseif ($task->sum < $user->balance && $template->balanceLimit == 0) {
            if ($this->factoryTasks->tasks->save($task)) {
                $template->postId = intval($task->itemId);
                $template->balanceRemain += $task->sum;

                if ($this->factoryAuto->auto->templates->save($template)) {
                    $user->balance -= $task->sum;
                    $this->factoryUsers->users->save($user);
                }
            }
        }

        return true;
    }

    protected function savePhoto($url)
    {

        $arr = explode('.', $url);
        $ext = array_pop($arr);

        $data = file_get_contents($url);
        $image = new Imagick();
        $image->readImageBlob($data);

        $path = 'tasks/' . rand(10, 99) . '/' . rand(10, 99) . '/' . rand(10, 99) . '/';
        $file = mb_substr(md5(Lib_Uuid::getNext()), 0, 11) . '.' . $ext;
        $file = strtok($file, '?'); //убираем все query параметры из строки (не понимаю откуда они берутся)


        if (!is_dir($path)) {
            @mkdir(IMAGES_PATH . $path, 0777, true);
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
}
