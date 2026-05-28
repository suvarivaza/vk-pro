<?php

namespace Service\Tasks;

class Controller_Shell_Tasks_Bot extends \System\Service_Controller_Shell
{
    protected $_fields = [
        'comments' => [
            'limit' => true,
            'comment' => 'Оставил комментарий',
        ],
        'join' => [
            'limit' => true,
            'comment' => 'Подписался на группу',
        ],
        'friends' => [
            'limit' => true,
            'comment' => 'Добавиться в друзья',
        ],
        'likes' => [
            'limit' => true,
            'comment' => 'Поставил лайк',
        ],
        'polls' => [
            'limit' => true,
            'comment' => 'Участие в опросе',
        ],
        'reposts' => [
            'limit' => true,
            'comment' => 'Репост',
        ],
        'video' => [
            'limit' => false,
            'comment' => 'Просмотр видео',
        ],
        'views' => [
            'limit' => false,
            'comment' => 'Просмотр поста',
        ],
    ];
    private $_limits = [];

    public function __construct()
    {
        parent::__construct();
        $this->_limits = json_decode(file_get_contents(Model_Config::$limitsPath), true);
    }

    /*
     * не используется!
     */
    public function A_Bot()
    {
        while (true) {
            $this->A_BotLikes(rand(3, 5));
            $this->A_BotJoin(rand(3, 5));
            $this->A_BotReposts(rand(3, 5));
            //$this->A_BotComment(rand(3,5));
            $this->A_BotPolls(rand(3, 5));
            sleep(rand(10, 60));
        }
    }

    public function A_BotLikes($limit = 0)
    {
        $query = $this->factoryUsers->users->query()->sortFunction('RAND()');

        if (intval($limit) > 1) {
            $query->limit(intval($limit));
        }

        $query->filter
            ->fieldValue('access_token', '!=', '')
            ->fieldValue('likesCount10Min', '<', intval($this->_limits['user']['likes']['interval']))
            ->fieldValue('likesCountHour', '<', intval($this->_limits['user']['likes']['hour']))
            ->fieldValue('likesCountDay', '<', intval($this->_limits['user']['likes']['day']));

        $it = $query->iteratorForSave();
        /** @var \Service\Users\Model_Users_User $user */
        foreach ($it as $user) {
            $arr = $this->factoryTasks->tasks->getItemsList($user, Model_Config::$botTypes[2], rand(1, 3), 0);

            foreach ($arr as $task) {
                $item_id = $task->itemId;

                if ($task->vkType == 'comment') {
                    $item_id = $task->commentId;
                }


                $response = $this->VK->likesAdd($task->vkType, $task->ownerId,$item_id, $task->ownerType, $user->access_token);

                if (isset($response['likes']) && $response['likes'] > 0) {
                    $this->taskSuccess($task, $user);
                } elseif ($response['error'] == 5 || $response['error'] == 17) {

                    // здесь нужно очищать токен специальным методом!
                    $user->access_token = '';
                    logMail( 'Vk-Pro.top clear access_token 7', "Vk-Pro.top clear access_token 7");

                    $this->factoryUsers->users->save($user);
                    break;
                } elseif ($response['error'] == 100) {
                    $this->taskStop($task);
                }
                usleep(rand(500000, 5000000));
            }
        }
    }

    protected function taskSuccess(Model_Tasks_Task $task, \Service\Users\Model_Users_User $user)
    {
        $field = 'price_' . $task->type . '_sell' . ($user->karma < 0 ? '_negative' : '') . ($user->karma >= 75 ? '_positive' : '');

        $task->makeShadow();
        $task->countReady++;
        $task->countMinute++;
        $task->count10Min++;
        $task->countHour++;
        $task->countDay++;
        $task->countRemain = $task->count - $task->countReady;
        $this->factoryTasks->tasks->save($task);

        $taskUser = $this->factoryTasks->users->getByTaskIdUserId($task->taskId, $user->userId, true);

        if ($taskUser === null) {
            $taskUser = $this->factoryTasks->users->getNew();
            $taskUser->taskId = $task->taskId;
            $taskUser->type = $task->type;
            $taskUser->userId = $user->userId;
            $taskUser->uid = $user->uid;
            $taskUser->isDel = false;
            $taskUser->isDone = false;
            $taskUser->isActive = true;
        }
        $taskUser->isDone = true;
        $taskUser->isDoneDate = time();
        $this->factoryTasks->users->save($taskUser);

        $user->makeShadow();

        if ($this->_fields[$task->type]['limit']) {
            $user->{$task->type . 'CountDay'}++;
            $user->{$task->type . 'CountHour'}++;
            $user->{$task->type . 'Count10Min'}++;
            $user->{$task->type . 'CountMinute'}++;
        }

        $balance = $this->factoryUsers->users->balance->getNew();
        $balance->userId = $user->userId;
        $balance->isTask = true;
        $balance->balance = floatval($this->settings[$field]);
        $balance->balanceFrom = $user->balance;
        $user->balance += $this->settings[$field];
        $balance->balanceTo = $user->balance;
        $balance->dateCreate = time();
        $balance->comment = $this->_fields[$task->type]['comment'];

        $karma = json_decode(file_get_contents(\Service\Users\Model_Config::$karmaPath), true);
        $karmaObj = $this->factoryUsers->users->karma->getNew();
        $karmaObj->userId = $user->userId;
        $karmaObj->karma = floatval($karma['karma'][$task->type . ($user->karma < 0 ? '_negative' : '')]);
        $karmaObj->karmaFrom = $user->karma;
        $user->karma += $karma['karma'][$task->type . ($user->karma < 0 ? '_negative' : '')];

        if ($user->karma > 100.00) {
            $user->karma = 100.00;
        }
        $karmaObj->karmaTo = $user->karma;
        $karmaObj->dateCreate = time();
        $karmaObj->comment = $this->_fields[$task->type]['comment'];

        if ($this->factoryUsers->users->save($user)) {
            $this->factoryUsers->users->karma->save($karmaObj);
            $this->factoryUsers->users->balance->save($balance);
        }

        if ($user->bonus && $this instanceof Controller_State_Client_List_Bonus) {
            $user->makeShadow();
            $bonus = \Service\Users\Model_Config::GetBonusSettings();
            $mConfig = \Service\Messages\Model_Config::GetConfig();

            $balanceBonus = $this->factoryUsers->users->balance->getNew();
            $balanceBonus->userId = $user->userId;
            $balanceBonus->isBonus = true;
            $balanceBonus->comment = 'Бонус за регистрацию';
            $balanceBonus->balance = floatval($bonus['register']);
            $balanceBonus->balanceFrom = $user->balance;
            $user->balance += $balanceBonus->balance;
            $balanceBonus->balanceTo = $user->balance;
            $balanceBonus->dateCreate = time();

            if ($this->factoryUsers->users->balance->save($balanceBonus)) {
                $user->bonus = 0;
                $this->factoryUsers->users->save($user);
            }

            $message = $this->factoryMessages->users->getNew();
            $message->userId = $user->userId;
            $message->isDone = false;
            $message->type = \Service\Messages\Model_Config::TYPE_SYSTEM;
            $text = $mConfig['bonus']['types']['regiter']['text'];
            $text = str_replace('%balance%', $balanceBonus->balance, $text);
            $message->text = $text;
            $message->icon = 'bonus';
            $this->factoryMessages->users->save($message);
        }

        return true;
    }

    public function A_BotJoin($limit = 0)
    {
        $query = $this->factoryUsers->users->query()->sortFunction('RAND()');

        if ($limit) {
            $query->limit($limit);
        }
        $query->filter->fieldValue('isBot', '&', 16)
            ->fieldValue('access_token', '!=', '')
            ->fieldValue('joinCount10Min', '<', intval($this->_limits['user']['join']['interval']))
            ->fieldValue('joinCountHour', '<', intval($this->_limits['user']['join']['hour']))
            ->fieldValue('joinCountDay', '<', intval($this->_limits['user']['join']['day']));

        $it = $query->iterator();
        /** @var \Service\Users\Model_Users_User $user */
        foreach ($it as $user) {
            $arr = $this->factoryTasks->tasks->getItemsList($user, Model_Config::$botTypes[16], rand(1, 2), 0);

            foreach ($arr as $task) {

                $response = $this->VK->groupsJoin($task->ownerId, $user->access_token);

                if ($response == 1 && !isset($response['error'])) {
                    $this->taskSuccess($task, $user);
                }

                usleep(rand(500000, 5000000));
            }
        }
    }

    public function A_BotReposts($limit = 0)
    {
        $query = $this->factoryUsers->users->query()->sortFunction('RAND()');

        if ($limit) {
            $query->limit($limit);
        }
        $query->filter->fieldValue('isBot', '&', 4)
            ->fieldValue('access_token', '!=', '')
            ->fieldValue('repostsCount10Min', '<', intval($this->_limits['user']['reposts']['interval']))
            ->fieldValue('repostsCountHour', '<', intval($this->_limits['user']['reposts']['hour']))
            ->fieldValue('repostsCountDay', '<', intval($this->_limits['user']['reposts']['day']));

        $it = $query->iterator();
        /** @var \Service\Users\Model_Users_User $user */
        foreach ($it as $user) {
            $arr = $this->factoryTasks->tasks->getItemsList($user, Model_Config::$botTypes[4], rand(1, 3), 0);
            $task = $arr[0];

            if (!$task) {
                continue;
            }

            $vkType = $task->vkType;

            if ($task->vkType == 'post') {
                $vkType = 'wall';
            }


            $response = $this->VK->makeRepost($vkType, $task->ownerId, $task->itemId, $task->ownerType,$user->access_token);

            if (isset($response['success']) && $response['success'] > 0) {
                $this->taskSuccess($task, $user);
            }
            usleep(rand(500000, 5000000));
        }
    }

    public function A_BotPolls($limit = 0)
    {
    }

    public function A_BotComment($limit = 0)
    {
        $query = $this->factoryUsers->users->query()->sortFunction('RAND()');

        if ($limit) {
            $query->limit($limit);
        }
        $query->filter->fieldValue('isBot', '&', 8)
            ->fieldValue('access_token', '!=', '')
            ->fieldValue('joinCount10Min', '<', intval($this->_limits['user']['join']['interval']))
            ->fieldValue('joinCountHour', '<', intval($this->_limits['user']['join']['hour']))
            ->fieldValue('joinCountDay', '<', intval($this->_limits['user']['join']['day']));

        $it = $query->iterator();
    }
}
