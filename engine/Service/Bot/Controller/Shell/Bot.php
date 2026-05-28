<?php

namespace Service\Bot;

use DateTime;
use Exception;
use Lib_DB_Factory;
use Lib_HSocket_Factory;
use Lib_Uuid;

use Service\Tasks\Model_Tasks;
use Service\Tasks\Model_Tasks_Task;
use Service\Users\Model_Notifications;
use Service\Users\Model_Users_Balances_Balance;
use Service\Users\Model_Users_User;
use STPL;
use System\Service_Controller_Shell;

/**
 * Class Controller_Shell_Bot
 */
class Controller_Shell_Bot extends Service_Controller_Shell
{
    /**
     * @var array
     */
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
    /**
     * @var array|mixed
     */
    private $_limits = [];

    /**
     * @var bool
     */
    private $_botAll = false;
    /**
     * @var int
     */
    private $_maxReady = 1;


    private $countTasksUnsuccessful = 0;
    private $countTasksSuccess = 0;
    private $countErrorsLikes = 0;
    private $countErrorsReposts = 0;
    private $countErrorsComments = 0;
    private $countErrorsJoin = 0;
    private $countErrorsFriends = 0;
    private $countErrorsPolls = 0;
    private $countCheckLimitsFalse = 0;
    private $countTasksForBot = 0;
    private $countTasksLikes = 0;
    private $countTasksReposts = 0;
    private $countTasksComments = 0;
    private $countTasksJoin = 0;
    private $countTasksFriends = 0;
    private $countTasksPolls = 0;
    private $countLikesSuccess = 0;
    private $countRepostsSuccess = 0;
    private $countCommentsSuccess = 0;
    private $countJoinSuccess = 0;
    private $countFriendsSuccess = 0;
    private $countPollsSuccess = 0;
    protected $isTokenClear = false;
    protected $countTokenClear = 0;
    protected $countWrongUsersTokens = 0;

    /**
     * Controller_Shell_Bot constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_limits = json_decode(file_get_contents(\Service\Tasks\Model_Config::$limitsPath), true);
    }

    /**
     * @throws \Lib_Exception_Logic_Backtraced
     * @throws \Lib_Exception_Runtime_Backtraced
     */
    public function A_ReturnTokens()
    {
        $sql = "SELECT * FROM `log_2019_04` WHERE `action` = 'vk-api-error' ORDER BY `logId` DESC";
        $res = $this->factoryBot->db->query($sql);

        while ($row = $res->fetch_assoc()) {
            $data = json_decode($row['params'], true);

            if ($data[1] != 'likes.add') {
                continue;
            }

            if ($data[3]['error']['error_code'] == 15) {
                $user = $this->factoryUsers->users->getById($row['userId'], true);

                if (!$user->access_token) {
                    $user->access_token = $data[2]['access_token'];
                    $this->factoryUsers->users->save($user);
                }
            }
        }
    }

    /**
     * @throws \Lib_Exception_InvalidArgument_Backtraced
     * @throws \Lib_Exception_Logic_Backtraced
     */
    public function A_BotTest0()
    {
        while (true) {
            $hour = date('H');

            if ($hour < 6) {
                return;
            }

            $query = $this->factoryBot->bots->query()->sortFunction('RAND()')->sqlCalcFoundRows(true);
            $query->filter->fieldValue('isActive', '=', true);
            $query->filter->fieldValue('isPro', '=', false);
            $query->filter->fieldValue('userId', '=', 36572);
            $it = $query->iteratorForSave();

            foreach ($it as $bot) {

                $user = $this->factoryUsers->users->getById($bot->userId);

                if ($user === null) {
                    continue;
                }

                if (!$user->access_token) {
                    continue;
                }


                $query = $this->factoryTasks->users->query();
                $query->filter->fieldValue('userId', '=', $user->userId)
                    ->fieldValue('isActive', '=', true);
                $ittaskUser = $query->iteratorForSave();

                foreach ($ittaskUser as $taskUser) {
                    $taskUser->isActive = false;
                    $this->factoryTasks->users->save($taskUser);
                }

                $id = 0;

                $result = false;
                $bot->makeShadow();

                switch (Model_Config::$ids[$id]) {
                    case 2:
                        $result = $this->A_BotLikes($bot, $user, 3);

                        break;
                    case 4:
                        $result = $this->A_BotReposts($bot, $user, 1);

                        break;
                    case 8:
                        $result = $this->A_BotComments($bot, $user, 1);

                        break;
                    case 16:
                        $result = $this->A_BotJoin($bot, $user, 1);

                        break;
                    case 32:
                        $result = $this->A_BotFriends($bot, $user, 1);

                        break;
                    case 64:
                        $result = $this->A_BotPolls($bot, $user, 1);

                        break;
                }

                $this->factoryBot->bots->save($bot);

                usleep(300000);
            }
            Lib_DB_Factory::Flush();
            Lib_HSocket_Factory::Flush();
            sleep(random_int(1, 3));
        }
    }

    /**
     * @param Model_Tasks_Task $task
     * @param Model_Users_User $user
     *
     * @return bool
     */
    protected function taskSuccess(Model_Tasks_Task $task, Model_Users_User $user)
    {
        $this->countTasksSuccess++;

        $field = 'price_' . $task->type . '_sell' . ($user->karma < 0 ? '_negative' : '') . ($user->karma >= 75 ? '_positive' : '');

        $task = $this->factoryTasks->tasks->getById($task->taskId, true);
        ++$task->countReady; //общее количество выполненных работ
        ++$task->countReadyBot; //количество работ выполлненных ботом

        if ($task->isSpecial && $task->isSpecialInvite && $task->type == 'join') {
            try {
                $special = $this->factoryTasks->specialGroups->getById($task->specialId);

                if ($special !== null) {
                    $specialUser = $this->factoryTasks->specialGroups->users->getNew();
                    $specialUser->specialId = $special->groupId;
                    $specialUser->userId = $user->userId;
                    $specialUser->dateCreate = time();
                    $this->factoryTasks->specialGroups->users->save($specialUser);
                }
            } catch (Exception $e) {
                //здесь нужно обработать ошибку
            }
        }
        $task->dateLast = time(); //ставим метку времени последнего выполнененного задания
        $task->countMinute++;
        $task->count10Min++;
        $task->countHour++;
        $task->countDay++;
        $task->countRemain = $task->count - $task->countReady; //посчитаем сколько осталось выполнить работ
        $this->factoryTasks->tasks->save($task);

        $taskUser = $this->factoryTasks->users->getByTaskIdUserId($task->taskId, $user->userId, true);

        if ($taskUser === null) {
            $taskUser = $this->factoryTasks->users->getNew();
            $taskUser->taskId = $task->taskId;
            $taskUser->type = $task->type;
            $taskUser->userId = $user->userId;
            $taskUser->uid = $user->uid;
            $taskUser->isDel = false;
            $taskUser->isActive = true;
        }
        $taskUser->isDone = true;
        $taskUser->isBot = true;
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
        $balance->isBot = true;
        $balance->balance = floatval($this->settings[$field]);
        $balance->balanceFrom = $user->balance;
        $user->balance += $this->settings[$field];
        $balance->balanceTo = $user->balance;
        $balance->dateCreate = time();
        $balance->comment = $this->_fields[$task->type]['comment'];

        $karma = json_decode(file_get_contents(\Service\Users\Model_Config::$karmaPath), true);
        $karmaObj = $this->factoryUsers->users->karma->getNew();
        $karmaObj->userId = $user->userId;
        $karmaObj->isBot = true;
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

        return true;
    }

    /**
     * @param Model_Bots_Bot $bot
     * @param Model_Users_User $user
     *
     * @throws \Lib_Exception_InvalidArgument_Backtraced
     * @throws \Lib_Exception_Logic_Backtraced
     */
    protected function tokenClearSendNotice(Model_Bots_Bot $bot, Model_Users_User $user)
    {

        if ($this->_botAll) {
            return;
        }

        STPL::PathRegister(ENGINE_PATH . 'engine/Service/Bot/Template/');

        $email = $this->factoryUsers->emails->getNew();
        $email->uuid = Lib_Uuid::getNext();
        $email->userId = $user->userId;
        $email->userEmail = $user->email;
        $email->dateCreate = time();
        $email->title = '[VK-PRO.TOP] Автобот. Закончилось действие токена';

        $this->factoryUsers->users->balance->setDate(strtotime('today'));
        $query = $this->factoryUsers->users->balance->query();
        $query->filter->fieldValue('userId', '=', $user->userId)
            ->fieldValue('isBot', '=', true);

        $it = $query->iterator();
        $totalBalance = 0;
        /** @var Model_Users_Balances_Balance $balance */
        foreach ($it as $balance) {
            $totalBalance += $balance->balance;
        }

        $html = STPL::Fetch('mail/token', [
            'user' => $user,
            'totalBalance' => $totalBalance,
        ]);

        $email->text = $html;
        $email->isSent = false;
        $email->isSentDate = null;
        $this->factoryUsers->emails->save($email);

        $notification = $this->factoryUsers->notifications->getNew();
        $notification->userId = $user->userId;
        $notification->type = Model_Notifications::TYPE_DAY0;
        $notification->service = 'bot';
        $notification->objectId = $bot->botId;
        $notification->title = 'Автобот приостановил работу по причине закончившегося срока действия токена';
        $this->factoryUsers->notifications->save($notification);

        $this->checkUserBan($user);
    }

    /**
     * @param Model_Users_User $user
     */
    protected function checkUserBan(Model_Users_User $user)
    {

        $result = $this->VK->getUser($user->uid, $this->getRandomCheckToken(), 'blacklisted');

        $data = $result[0];

        if (isset($data['deactivated']) && $data['deactivated'] == 'banned' && !$user->ban) {
            if (!$user->ban) {
                $user->makeShadow();
                $user->ban = true;
                $user->banDate = time();
                $karmaObj = $this->factoryUsers->users->karma->getNew();
                $karmaObj->userId = $user->userId;
                $karmaObj->karma = -500.00;
                $karmaObj->karmaFrom = $user->karma;
                $user->karma = -500.00;
                $karmaObj->karmaTo = $user->karma;
                $karmaObj->dateCreate = time();
                $karmaObj->comment = 'Списывание кармы за бан в ВК';
                $this->factoryUsers->users->karma->save($karmaObj);

                $factoryMessages = new \Service\Messages\Model_Factory();
                $message = $factoryMessages->users->getNew();
                $message->userId = $user->userId;
                $message->isDone = false;
                $message->type = \Service\Messages\Model_Config::TYPE_SYSTEM;
                $text = 'Вам был начислен штраф за бан страницы в ВК';
                $message->text = $text;
                $message->icon = 'vkpro';
                $this->factoryMessages->users->save($message);
            }
        }
    }

    /*
     * чекаем лимиты и устанавливаем lastJoin lastLike lastRepost lastComment lastFriends lastPoll
     *
     * проверяются интервалы между работами (чтобы работы не выполнялись слишком часто)
     * и лимиты по количеству выполняемых работ за заданный период времени
     */
    private function checkLimits(Model_Bots_Bot $bot, Model_Users_User $user, $botType)
    {

        switch ($botType) {
            case 'join':
                // ограничение по интервалу между выполненными работами! чтобы работы не выполнялись слишком часто!
                // расчитаем среднее значение нужного интервала между работами
                // 600 секунд/10 минут делим на установленное ограничение выполнляемых работ за 10 минут
                $interval = 600 / intval($this->_limits['user']['join']['interval']);
                if ($bot->lastJoin > (time() - $interval)) return false;

                //ограничения по количеству выполненных работ за определенный промежуток времени
                if ($user->joinCount10Min > intval($this->_limits['user']['join']['interval'])) return false;
                if ($user->joinCountHour > intval($this->_limits['user']['join']['hour'])) return false;
                if ($user->joinCountDay > intval($this->_limits['user']['join']['day'])) return false;

                $bot->lastJoin = time();
                break;
            case 'likes':

                $interval = 600 / intval($this->_limits['user']['likes']['interval']);
                if ($bot->lastLike > (time() - $interval)) return false;

                if ($user->likesCount10Min > intval($this->_limits['user']['likes']['interval'])) return false;
                if ($user->likesCountHour > intval($this->_limits['user']['likes']['hour'])) return false;
                if ($user->likesCountDay > intval($this->_limits['user']['likes']['day'])) return false;

                $bot->lastLike = time();
                break;
            case 'reposts':

                $interval = 600 / intval($this->_limits['user']['reposts']['interval']);
                if ($bot->lastRepost > (time() - $interval)) return false;

                if ($user->repostsCount10Min > intval($this->_limits['user']['reposts']['interval'])) return false;
                if ($user->repostsCountHour > intval($this->_limits['user']['reposts']['hour'])) return false;
                if ($user->repostsCountDay > intval($this->_limits['user']['reposts']['day'])) return false;

                $bot->lastRepost = time();
                break;
            case 'comments':

                $interval = 600 / intval($this->_limits['user']['comments']['interval']);
                if ($bot->lastComment > (time() - $interval)) return false;

                if ($user->commentsCount10Min > intval($this->_limits['user']['comments']['interval'])) return false;
                if ($user->commentsCountHour > intval($this->_limits['user']['comments']['hour'])) return false;
                if ($user->commentsCountDay > intval($this->_limits['user']['comments']['day'])) return false;

                $bot->lastComment = time();
                break;
            case 'friends':

                $interval = 600 / intval($this->_limits['user']['friends']['interval']);
                if ($bot->lastFriends > (time() - $interval)) return false;

                if ($user->friendsCount10Min > intval($this->_limits['user']['friends']['interval'])) return false;
                if ($user->friendsCountHour > intval($this->_limits['user']['friends']['hour'])) return false;
                if ($user->friendsCountDay > intval($this->_limits['user']['friends']['day'])) return false;

                $bot->lastFriends = time();
                break;
            case 'polls':

                $interval = 600 / intval($this->_limits['user']['polls']['interval']);
                if ($bot->lastPoll > (time() - $interval)) return false;

                if ($user->pollsCount10Min > intval($this->_limits['user']['polls']['interval'])) return false;
                if ($user->pollsCountHour > intval($this->_limits['user']['polls']['hour'])) return false;
                if ($user->pollsCountDay > intval($this->_limits['user']['polls']['day'])) return false;

                $bot->lastPoll = time();
                break;

        }

        return true;
    }

    /**
     * @param $user
     * @param $botType
     * @param $limit
     * @param false $logDb
     * @return Model_Tasks_Task[]
     */
    private function getTasksForBot($user, $botType, $limit, $logDb = false)
    {

        //Получаем задания доступные для выполнения указанному пользователю
        //$lastTime = time() - rand(1, 4); //рандомное смещение по времени для выборки заданий?
        $lastTime = null;

        //установил limit = 100 для того чтобы рандомизировать выборку заданий
        //далее перемешываем полученный массив и берем первые $limit заданий
        $list = $this->factoryTasks->tasks->getListSpecials($user, Model_Config::$botTypes[$botType], 100, 0, $lastTime);
        $arr = $this->factoryTasks->tasks->getItemsList($user, Model_Config::$botTypes[$botType], 100, 0, 0, $lastTime,
            $logDb, $this->_maxReady);

        $tasks = array_merge($list, $arr);
        shuffle($tasks);
        $tasks = array_slice($tasks, 0, $limit); //обрезаем полученный массив до указанного лимита

        $this->countTasksForBot += count($tasks);

        return $tasks;
    }


    /**
     * @param Model_Bots_Bot $bot
     * @param Model_Users_User $user
     * @throws \Lib_Exception_InvalidArgument_Backtraced
     * @throws \Lib_Exception_Logic_Backtraced
     */
    private function tokenClear(Model_Bots_Bot $bot, Model_Users_User $user)
    {
        $user->makeShadow();
        $user->access_token = '';
        $this->factoryUsers->users->save($user);
        $this->tokenClearSendNotice($bot, $user);
        $this->isTokenClear = true;
        $this->countTokenClear++;

    }

    /**
     * работает!
     * @param Model_Bots_Bot $bot
     * @param Model_Users_User $user
     * @param int $limit
     *
     * @return bool
     */
    public function A_BotLikes(Model_Bots_Bot $bot, Model_Users_User $user, $limit = 0)
    {


        if (!$this->checkLimits($bot, $user, 'likes')) {
            $this->countCheckLimitsFalse++;
            return false;
        }


        //получаем задания для выполнения текущим пользователем
        $tasks = $this->getTasksForBot($user, 2, $limit, true);
        $this->countTasksLikes += count($tasks);

        //если для пользователя нет заданий то выходим
        if (!count($tasks)) return false;

        //выполняем задания по очереди
        foreach ($tasks as $task) {

            //то это?
//            if ($task->countReadyBot / $task->count > $this->_maxReady) {
//                continue;
//            }

            $item_id = $task->itemId;

            //если нужно поставить лайк на комментарий
            if ($task->vkType == 'comment') {
                $item_id = $task->commentId;
            }

            $response = $this->VK->likesAdd($task->vkType, $task->ownerId, $item_id, $task->ownerType, $user->access_token);

            if (!empty($response['likes'])) {
                //задание выполнено успешно
                $this->countLikesSuccess++;
                $isSuccess = $this->taskSuccess($task, $user);
            } elseif ($response['error'] == 5) {
                //не рабочий токен. удалим токен у пользователя и уведомим пользователя о том что нужно заменить токен
                $this->tokenClear($bot, $user);
                $this->countErrorsLikes++;
            } elseif ($response['error'] == 15 && $response['errorText'] === 'Access denied: no access to call this method') {
                //если у токена нет доступа к вызываемому методу
                $this->tokenClear($bot, $user);
                $this->countErrorsLikes++;
            } elseif ($response['error'] == 17 && $response['errorText'] === 'Validation required: please open redirect_uri in browser') {
                $this->tokenClear($bot, $user);
                $this->countErrorsLikes++;
            } elseif ($response['error'] == 27 && $response['errorText'] === 'Group authorization failed: method is unavailable with group auth.') {
                $this->tokenClear($bot, $user);
                $this->countErrorsLikes++;
            } elseif ($response['error'] == 39 && $response['errorText'] === 'Unknown user: could not get current user') {
                $this->tokenClear($bot, $user);
                $this->countErrorsLikes++;
            } elseif ($response['error'] == 38 && $response['errorText'] === 'Unknown application: could not get application') {
                $this->tokenClear($bot, $user);
                $this->countErrorsLikes++;
            } elseif ($response['error'] == 3610 && $response['errorText'] === 'User is deactivated: invalid access_token (8).') {
                $this->tokenClear($bot, $user);
                $this->countErrorsLikes++;
            } elseif ($response['error'] == 15 && $response['errorText'] === 'Access denied: this profile is private') {
                //приватный профиль
                $this->taskStop($task, 'Невозможно поставить лайк! Приватный профиль!');
                $this->countErrorsLikes++;
            } elseif ($response['error'] == 15 && $response['errorText'] === 'Access denied') {
                //приватный профиль
                $this->taskStop($task, 'Нет доступа к посту!');
                $this->countErrorsLikes++;
            } elseif ($response['error'] == 15 && $response['errorText'] === 'Access denied: no access to this group') {
                //нет доступа к группе
                $this->taskStop($task, 'Невозможно поставить лайк! Группа недоступна!');
                $this->countErrorsLikes++;
            } elseif ($response['error'] == 30 && $response['errorText'] === 'This profile is private') {
                //приватный профиль
                $this->taskStop($task, 'Невозможно поставить лайк! Приватный профиль!');
                $this->countErrorsLikes++;
            } elseif ($response['error'] == 100 && $response['errorText'] === 'One of the parameters specified was missing or invalid: object not found') {
                //не найден объект
                $this->taskStop($task, 'Невозможно поставить лайк! Пост не найден!');
                $this->countErrorsLikes++;
            } elseif ($response['error'] == 100 && $response['errorText'] === 'One of the parameters specified was missing or invalid') {
                //невозможно выполнить репост этой записи!
                $this->taskStop($task, 'Нет доступа к посту!');
                $this->countErrorsLikes++;
            } elseif ($response['error'] == 100 && $response['errorText'] === 'One of the parameters specified was missing or invalid: item_id is undefined') {
                //невозможно выполнить репост этой записи!
                $this->taskStop($task, 'Нет доступа к посту!');
                $this->countErrorsLikes++;
            } else {
                logs([
                    'method' => 'A_BotLikes',
                    'VkMethod' => 'likesAdd',
                    'request' => [
                        'vkType' => $task->vkType,
                        'ownerId' => $task->ownerId,
                        'item_id' => $item_id,
                        'ownerType' => $task->ownerType,
                        'access_token' => $user->access_token,
                    ],
                    'response' => $response
                ], 'crons/bot.log');
                //не определенная ошибка
                //logMail('Vk-Pro.top Bot->BotLikes error', "Vk-Pro.top Bot->BotLikes error: " . print_r($response, true));
                $this->countErrorsLikes++;
            }

            // если у пользователя был очищен токен то сразу возвращаем false (без токена задания выполнять не получится!)
            if ($this->isTokenClear) return false;
            usleep(300000);

        }

        //прекращаем цикл если удалось выполлнить хотябы одно задание
        if (!empty($isSuccess)) return true;
        return false;

    }

    /**
     * работает!
     * Делает репосты
     * @param Model_Bots_Bot $bot
     * @param Model_Users_User $user
     * @param int $limit
     *
     * @return bool
     *
     * @throws \Lib_Exception_InvalidArgument_Backtraced
     * @throws \Lib_Exception_Logic_Backtraced
     */
    public function A_BotReposts(Model_Bots_Bot $bot, Model_Users_User $user, $limit = 0)
    {


        if (!$this->checkLimits($bot, $user, 'reposts')) {
            $this->countCheckLimitsFalse++;
            return false;
        }

        $tasks = $this->getTasksForBot($user, 4, $limit);

        if (!count($tasks)) return false;
        $this->countTasksReposts += count($tasks);

        foreach ($tasks as $task) {

//            if ($task->countReadyBot / $task->count > $this->_maxReady) {
//                continue;
//            }

            $vkType = $task->vkType;

            if ($task->vkType == 'post') {
                $vkType = 'wall';
            }

            if ($vkType == 'comment') {
                $vkType = 'wall';
                $itemId = $task->itemId . '_r' . $task->commentId;
            } else {
                $itemId = $task->itemId;
            }

            $response = $this->VK->makeRepost($vkType, $task->ownerId, $itemId, $task->ownerType, $user->access_token);

            if (!empty($response['success'])) {
                //задание выполнено успешно
                $this->countRepostsSuccess++;
                $isSuccess = $this->taskSuccess($task, $user);
            } else if ($response['error'] == 15 && $response['errorText'] === 'Access denied: no access to call this method') {
                $this->tokenClear($bot, $user);
                $this->countErrorsReposts++;
            } elseif ($response['error'] == 15 && $response['errorText'] === 'Access denied: can not publish this type of posts') {
                //невозможно выполнить репост этой записи!
                $this->taskStop($task, 'Невозможно сделать репост!');
                $this->countErrorsReposts++;
            } elseif ($response['error'] == 15 && $response['errorText'] === 'Access denied: post was deleted') {
                //невозможно выполнить репост этой записи!
                $this->taskStop($task, 'Невозможно сделать репост! Пост был удален!');
                $this->countErrorsReposts++;
            } elseif ($response['error'] == 15 && $response['errorText'] === "Access denied: can't publish") {
                //невозможно выполнить репост этой записи!
                $this->taskStop($task, 'Невозможно сделать репост! Пост был удален!');
                $this->countErrorsReposts++;
            } elseif ($response['error'] == 15 && $response['errorText'] === "Access denied: group is blocked") {
                //невозможно выполнить репост этой записи!
                $this->taskStop($task, 'Невозможно сделать репост! Группа заблокирована!');
                $this->countErrorsReposts++;
            } elseif ($response['error'] == 100 && $response['errorText'] === 'One of the parameters specified was missing or invalid') {
                //невозможно выполнить репост этой записи!
                $this->taskStop($task, 'Не удается получить пост!');
                $this->countErrorsReposts++;
            } elseif ($response['error'] == 100 && $response['errorText'] === 'One of the parameters specified was missing or invalid: video has restrictions') {
                //невозможно выполнить репост этой записи!
                $this->taskStop($task, 'Не удается выполнить репост! Видео недоступно!');
                $this->countErrorsReposts++;
            } else {

                logs([
                    'method' => 'A_BotReposts',
                    'VkMethod' => 'makeRepost',
                    'request' => [
                        'vkType' => $vkType,
                        'ownerId' => $task->ownerId,
                        'item_id' => $itemId,
                        'ownerType' => $task->ownerType,
                        'access_token' => $user->access_token,
                    ],
                    'response' => $response
                ], 'crons/bot.log');

                //не определенная ошибка
                //logMail('Vk-Pro.top Bot->BotReposts error', "Vk-Pro.top Bot->BotReposts error: " . print_r($response, true));
                $this->countErrorsReposts++;
            }

            if ($this->isTokenClear) return false;
            usleep(300000);

        }

        if (!empty($isSuccess)) return true;
        return false;
    }

    /**
     * работает!
     * @param Model_Bots_Bot $bot
     * @param Model_Users_User $user
     * @param int $limit
     *
     * @return bool|void
     */
    public function A_BotComments(Model_Bots_Bot $bot, Model_Users_User $user, $limit = 0)
    {


        if (!$this->checkLimits($bot, $user, 'comments')) {
            $this->countCheckLimitsFalse++;
            return false;
        }

        $tasks = $this->getTasksForBot($user, 8, $limit);

        if (!count($tasks)) return false;
        $this->countTasksComments += count($tasks);

        foreach ($tasks as $task) {

//            if ($task->countReadyBot / ($task->countReady + $task->countRemain) > $this->_maxReady) {
//                continue;
//            }

            switch ($task->commentType) {
                case 1:
                    $comments = [
                        'Круто!',
                        'Класс!',
                        'Классно!',
                        'Мне нравится!',
                        'Здорово!',
                        'Прикольно!',
                        'Ништяк!',
                        'Красота!',
                        'Вот это да!',
                        'Как по мне  норм.',
                        'Весело))',
                        'Весело!,Неплохо!',
                        'Афигенно!',
                        'Вот это здорово!',
                        'Ура!',
                        'Вау!',
                        'Вааау))',
                        'Зачётно!',
                        'Прекрасно!)',
                        'Просто супер!',
                        'Великолепно!',
                        'Невообразимо!)',
                        'Клёво!',
                        'Супер!)',
                        'Тема)',
                        'Замечательно!',
                        'Нормас',
                    ];
                    break;
                case 2:
                    $comments = [
                        'Отстой',
                        'Мне не нравится',
                        'Так себе',
                        'Некрасиво',
                        'Фу!',
                        'Ну такое',
                        'Не очень',
                        'Ну как-то так',
                        'Безпонтово',
                        'Шляпа',
                        'Вата',
                        'Бред',
                        'Фигня',
                        'Хрень',
                        'Пипец',
                        'Капец',
                        'Жесть',
                        'Писос',
                        'Тупость',
                        'Стрём',
                        'Ересь',
                    ];
                    break;
                case 3:
                    $comments = $task->getComments();
                    break;
                default:
                    $comments = [
                        'Круто!',
                        'Класс!',
                        'Классно!',
                        'Мне нравится!',
                        'Здорово!',
                        'Прикольно!',
                        'Ништяк!',
                        'Красота!',
                        'Вот это да!',
                        'Как по мне  норм.',
                        'Весело))',
                        'Весело!,Неплохо!',
                        'Афигенно!',
                        'Вот это здорово!',
                        'Ура!',
                        'Вау!',
                        'Вааау))',
                        'Зачётно!',
                        'Прекрасно!)',
                        'Просто супер!',
                        'Великолепно!',
                        'Невообразимо!)',
                        'Клёво!',
                        'Супер!)',
                        'Тема)',
                        'Замечательно!',
                        'Нормас',
                    ];
            }

            if (!count($comments)) continue;

            $comment = $comments[rand(0, count($comments) - 1)];
            $response = $this->VK->createComment($task->ownerId, $task->itemId, $task->ownerType, $task->vkType, $comment, Lib_Uuid::getNext(), $user->access_token);

            if (!empty($response['comment_id'])) {
                $this->countCommentsSuccess++;
                $isSuccess = $this->taskSuccess($task, $user);
            } elseif ($response['error'] == 10 && $response['errorText'] === 'Internal server error: parent deleted') {
                $this->taskStop($task, 'Невозможно выполнить комментарий! Нет доступа к посту!');
                $this->countErrorsComments++;
            } elseif ($response['error'] == 213 && $response['errorText'] === 'Access to status replies denied: replies disabled') {
                $this->taskStop($task, 'Невозможно выполнить комментарий! Комментарии отключены!');
                $this->countErrorsComments++;
            } elseif ($response['error'] == 213 && $response['errorText'] === 'Access to status replies denied') {
                $this->taskStop($task, 'Невозможно выполнить комментарий! Комментарии отключены!');
                $this->countErrorsComments++;
            } elseif ($response['error'] == 15 && $response['errorText'] === 'Access denied') {
                $this->taskStop($task, 'Невозможно выполнить комментарий! Доступ закрыт!');
                $this->countErrorsComments++;
            } else {

                logs([
                    'method' => 'A_BotComments',
                    'VkMethod' => 'createComment',
                    'request' => [
                        'ownerId' => $task->ownerId,
                        'item_id' => $task->itemId,
                        'ownerType' => $task->ownerType,
                        'vkType' => $task->vkType,
                        'access_token' => $user->access_token,
                    ],
                    'response' => $response
                ], 'crons/bot.log');

                //не определенная ошибка
                //logMail('Vk-Pro.top Bot->BotComments error', "Vk-Pro.top Bot->BotComments error: " . print_r($response, true));
                $this->countErrorsComments++;
            }


            if ($this->isTokenClear) return false;
            usleep(300000);

        }

        //если хотябы одно задание удалось выполнить вернем успешный результат
        if (!empty($isSuccess)) return true;
        return false;
    }



    /**
     * работает!
     * @param Model_Bots_Bot $bot
     * @param Model_Users_User $user
     * @param int $limit
     * @param bool $unsubscribe
     *
     * @return bool
     *
     * @throws \Lib_Exception_InvalidArgument_Backtraced
     * @throws \Lib_Exception_Logic_Backtraced
     */
    public function A_BotJoin(
        Model_Bots_Bot   $bot,
        Model_Users_User $user,
                         $limit = 0,
                         $unsubscribe = false,
                         $isTest = false
    )
    {

        //чекаем лимиты указанные в настройках задания
        if (!$this->checkLimits($bot, $user, 'join')) {
            $this->countCheckLimitsFalse++;
            return false;
        }


        //получаем задания подходящие для выполнения данным пользователем
        $tasks = $this->getTasksForBot($user, 16, $limit);

        if (!count($tasks)) return false;
        $this->countTasksJoin += count($tasks);

        foreach ($tasks as $task) {

            //if ($task->taskId == 191366) continue;

            //странное условие/ проверить!
//            if ($task->countReadyBot / $task->count > $this->_maxReady) {
//                continue;
//            }

            //подписываем пользователя в группу
            $response = $this->VK->groupsJoin($task->ownerId, $user->access_token);

            if ($response == 1 && !isset($response['error'])) {
                if ($unsubscribe) {
                    usleep(300000);
                    $response = $this->VK->newsfeedAddBan($task->ownerId, $user->access_token);
                }
                $this->countJoinSuccess++;
                $isSuccess = $this->taskSuccess($task, $user);
            } else if ($response['error'] == 15 && $response['errorText'] === 'Access denied: no access to call this method') {
                $this->tokenClear($bot, $user);
                $this->countErrorsJoin++;
            } else if ($response['error'] == 15 && $response['errorText'] === 'Access denied: no access to this group') {
                $this->taskStop($task, 'Невозможно подписаться! Группа недоступна!');
                $this->countErrorsJoin++;
            } else if ($response['error'] == 15 && $response['errorText'] === "Access denied: you can't join this private community") {
                $this->taskStop($task, 'Невозможно подписаться! Группа приватная!');
                $this->countErrorsJoin++;
            } else if ($response['error'] == 15 && $response['errorText'] === 'Access denied: you are already in this community') {
                //пользователь уже подписан на эту группу.
                //решил считать такой случай за выполнение задания! чтобы оно опять не выводилось этому пользователю
                $this->countJoinSuccess++;
                $isSuccess = $this->taskSuccess($task, $user);
            } else if ($response['error'] == 15 && $response['errorText'] === 'Access denied: you in group blacklist') {
                //пользователь в черном списке группы.
                $this->taskStop($task, 'Невозможно подписаться! Отключите черный список в группе!');
                $this->countErrorsJoin++;
            } else if ($response['error'] == 103 && $response['errorText'] === 'Out of limits: max groups count') {
                //103 Превышено ограничение на количество вступлений. что это вообще и что здесь делать?
                //$this->taskStop($task, 'Out of limits: max groups count');
                //$this->countErrorsJoin++;
            } else if ($response['error'] == 14 && $response['errorText'] === 'Captcha needed') {
                //так можно обработать - https://vk.com/dev/captcha_error
                $this->countErrorsJoin++;
            } else {
                //не определенная ошибка
                //logMail('Vk-Pro.top Bot->BotJoin error', "Vk-Pro.top Bot->BotJoin error: " . print_r($response, true));
                $this->countErrorsJoin++;
            }


            if ($this->isTokenClear) return false;
            usleep(300000);

        }

        //если хотябы одно задание удалось выполнить вернем успешный результат
        if (!empty($isSuccess)) return true;
        else return false;
    }

    /**
     * похоже работает!
     * @param Model_Bots_Bot $bot
     * @param Model_Users_User $user
     * @param int $limit
     *
     * @return bool
     *
     * @throws \Lib_Exception_InvalidArgument_Backtraced
     * @throws \Lib_Exception_Logic_Backtraced
     */
    public function A_BotFriends(Model_Bots_Bot $bot, Model_Users_User $user, $limit = 0)
    {


        if (!$this->checkLimits($bot, $user, 'friends')) {
            $this->countCheckLimitsFalse++;
            return false;
        }

        $tasks = $this->getTasksForBot($user, 32, $limit);

        if (!count($tasks)) return false;
        $this->countTasksFriends += count($tasks);

        foreach ($tasks as $task) {

//            if ($task->countReadyBot / $task->count > $this->_maxReady) {
//                continue;
//            }

            $response = $this->VK->friendsAdd($task->ownerId, $user->access_token);

            if ($response > 0 && !isset($response['error'])) {
                $this->countFriendsSuccess++;
                $isSuccess = $this->taskSuccess($task, $user);
            } else if ($response['error'] == 15 && $response['errorText'] === 'Access denied: no access to call this method') {
                $this->tokenClear($bot, $user);
                $this->countErrorsFriends++;
            } elseif ($response['error'] == 177 && $response['errorText'] === "Cannot add this user to friends as user not found") {
                //пользователь не найден (заблокирован или удален)
                $this->taskStop($task, 'Невозможно добавить в друзья! Пользователь недоступен!');
                $this->countErrorsFriends++;
            } else {
                //не определенная ошибка
                //logMail('Vk-Pro.top Bot->BotFriends error', "Vk-Pro.top Bot->BotFriends error: " . print_r($response, true));
                $this->countErrorsFriends++;
            }


            if ($this->isTokenClear) return false;
            usleep(300000);

        }

        //если хотябы одно задание удалось выполнить вернем успешный результат
        if (!empty($isSuccess)) return true;
        else return false;
    }

    /**
     * не работает!
     * @param Model_Bots_Bot $bot
     * @param Model_Users_User $user
     * @param int $limit
     *
     * @return bool
     */
    public function A_BotPolls(Model_Bots_Bot $bot, Model_Users_User $user, $limit = 0)
    {

        /////////////////

        //$response = $this->VK->getVoteById('132398802',2, '372497628', $user->access_token);

//        $response = $this->VK->addVote('132398802',2, '372497628', '1244862741', $user->access_token);
//        print_r($response);
//        die;

        //////////////////

        if (!$this->checkLimits($bot, $user, 'polls')) {
            $this->countCheckLimitsFalse++;
            return false;
        }

        $tasks = $this->getTasksForBot($user, 64, $limit);

        if (!count($tasks)) return false;
        $this->countTasksPolls += count($tasks);

        foreach ($tasks as $task) {

//            if ($task->countReadyBot / $task->count > $this->_maxReady) {
//                continue;
//            }

            $answerIds = explode(',', $task->answerIds);

            //костыль. убираем пустые answerId из массива
            foreach ($answerIds as $id => $answerId) {
                if (!$answerId) {
                    unset($answerIds[$id]);
                }
            }


            //костыль? если для голосования выбран любой вариант answerId == 0 и по какой то причине нету $answerIds то получим их
            if ($task->answerId == 0 and !count($answerIds)) {

                $response = $this->VK->getVoteById($task->ownerId, $task->ownerType, $task->pollId, $user->access_token);

                if (!empty($response['error'])) {

                    if ($response['error'] == 250 && $response['errorText'] === 'Access to poll denied') {
                        //нет доступа к опросу
                        $this->taskStop($task, 'Нет доступа к опросу!');
                        $this->countErrorsPolls++;

                    } else {

//                        logs([
//                            'method' => 'A_BotPolls',
//                            'VkMethod' => 'getVoteById',
//                            'request' => [
//                                'ownerId' => $task->ownerId,
//                                'pollId' => $task->pollId,
//                                'access_token' => $user->access_token,
//                            ],
//                            'response' => $response
//                        ],'crons/bot.log');

                    }

                    return false;
                }

                if (isset($response['answers']) and is_array($response['answers'])) {
                    foreach ($response['answers'] as $answer) {
                        $answerIds[] = $answer['id'];
                    }
                }

                if (is_array($answerIds) && count($answerIds) > 0) {
                    $task->makeShadow();
                    $task->answerIds = implode(',', $answerIds);
                    $this->factoryTasks->tasks->save($task);
                }
                usleep(300000);

            }


            //возьмем рандомный вариант ответа
            shuffle($answerIds);
            $randomAnswerId = array_shift($answerIds);
            $answer_ids = $task->answerId ?: $randomAnswerId;


            //выполняем голосование
            $response = $this->VK->addVote($task->ownerId, $task->ownerType, $task->pollId, $answer_ids, $user->access_token);

            if ($response == 1 && !isset($response['error'])) {
                $this->countPollsSuccess++;
                $isSuccess = $this->taskSuccess($task, $user);
            } else if ($response['error'] == 250 && $response['errorText'] === 'Access to poll denied') {
                //нет доступа к опросу
                $this->taskStop($task, 'Нет доступа к опросу!');
                $this->countErrorsPolls++;
            }
//            else if ($response['error'] == 5 && $response['errorText'] === 'User authorization failed: invalid session.') {
//                //похоже здесь мертный токен
//                $this->countErrorsPolls++;
//            } else if ($response['error'] == 5 && $response['errorText'] === 'User authorization failed: user is blocked.') {
//                $this->countErrorsPolls++;
//            } else if ($response['error'] == 39 && $response['errorText'] === 'Unknown user: could not get current user') {
//                $this->countErrorsPolls++;
//            } else if ($response['error'] == 5 && $response['errorText'] === 'User authorization failed: user revoke access for this token.') {
//                $this->countErrorsPolls++;
//            } else if ($response['error'] == 5 && $response['errorText'] === 'User authorization failed: invalid access_token (4).') {
//                $this->countErrorsPolls++;
//            } else if ($response['error'] == 38 && $response['errorText'] === 'Unknown application: could not get application') {
//                $this->countErrorsPolls++;
//            }
            else {

                logs([
                    'method' => 'A_BotPolls',
                    'VkMethod' => 'addVote',
                    'request' => [
                        'ownerId' => $task->ownerId,
                        'pollId' => $task->pollId,
                        'answer_ids' => $answer_ids,
                        'access_token' => $user->access_token,
                    ],
                    'response' => $response
                ], 'crons/bot.log');

                //не определенная ошибка
                //logMail('Vk-Pro.top Bot->BotPolls error', "Vk-Pro.top Bot->BotPolls error: " . print_r($response, true));
                $this->countErrorsPolls++;
            }

//            print_r([$task->url, $task->ownerId, $task->pollId, $answer_ids, $user->access_token]);
//            print_r($response);
//            die;


            usleep(300000);
        }

        //если хотябы одно задание удалось выполнить вернем успешный результат
        if (!empty($isSuccess)) return true;
        else return false;
    }


    /**
     * @throws \Lib_Exception_InvalidArgument_Backtraced
     * @throws \Lib_Exception_Logic_Backtraced
     */
    public function A_UpdateActive()
    {
        $query = $this->factoryBot->bots->query();
        $query->filter->fieldValue('isPro', '=', true);
        $it = $query->iteratorForSave();
        /** @var Model_Bots_Bot $bot */
        foreach ($it as $bot) {
            if ($bot->dateValid < time()) {
                $bot->isPro = false;
                $this->factoryBot->bots->save($bot);
            }
        }
    }

    /**
     * @throws \Lib_Exception_InvalidArgument_Backtraced
     * @throws \Lib_Exception_Logic_Backtraced
     */
    public function A_Prepare()
    {
        //это не запускается!
        //создание ботов для пользователей у которых включен бот
        //непонятно нужно ли это???!
        //выбираем пользователей у которых активировано автоматическое выполнение заданий (isBot > 0)
        //в isBot у пользователя будет айди активного бота
        $query = $this->factoryUsers->users->query();
        $query->filter->fieldValue('isBot', '>', 0);
        $query->filter->fieldValue('access_token', '!=', '');

        $it = $query->iterator();
        /** @var Model_Users_User $user */
        foreach ($it as $user) {

            //получаем бота пользователя
            $bot = $this->factoryBot->bots->getByUserId($user->userId);

            //Если нет бота, то создаем его. как так получается? почему ботам может не быть???
            //В какой момент создается бот в БД???
            if ($bot === null) {
                $bot = $this->factoryBot->bots->getNew();
                $bot->userId = $user->userId;
                $bot->isBot = $user->isBot;
                $bot->isPro = false;
                $bot->isActive = true;
                $bot->dateCreate = time();
                $bot->dateValid = null;
                $this->factoryBot->bots->save($bot);
            }
        }
    }

    /*
    * //Ночью не запускаем бота до 6 утра!
    */
    private function checkHours()
    {
        //return true;
        $hour = date('H');
        if ($hour < 6) {
            return false;
        } else {
            return true;
        }
    }


    private function checkUserAccessToken(Model_Users_User $user)
    {

        $response = $this->VK->checkUserAccessToken($user->access_token);

        if (!empty($response['error'])) {
            $this->countWrongUsersTokens++;
//            $user->makeShadow();
//            $user->access_token = '';
//            $this->factoryUsers->users->save($user);
            echo "\n" . $response['errorText'] . " user->access_token: $user->access_token";
            return false;
        }

        return true;
    }


    /**
     * @throws \Lib_Exception_InvalidArgument_Backtraced
     * @throws \Lib_Exception_Logic_Backtraced
     */
    public function A_BotAll_NotUsed()
    {
        $this->_botAll = true;
        $this->_maxReady = 0.8;

        //получаем всех пользователей у которых включен бот
        $query = $this->factoryUsers->users->query()->sortFunction('RAND()')->sqlCalcFoundRows(true);
        $query->filter->fieldValue('access_token', '!=', '');
        $query->filter->fieldCollection('userId', 'NOT IN', [1, 2, 3, 11666]);
        $query->filter->fieldValue('isBot', '=', 0);

        $it = $query->iterator();

        $time = time();
        /** @var Model_Users_User $user */
        foreach ($it as $user) {

            //получаем бота пользователя
            $bot = $this->factoryBot->bots->getByUserId($user->userId);

            if ($bot !== null) {
                continue;
            }

            if (time() - $time > 60) {
                $time = time();
                Lib_DB_Factory::Flush();
                Lib_HSocket_Factory::Flush();
            }

            if ($user->karma < 0) {
                continue;
            }

            if ($user->balance < 0) {
                continue;
            }

            $bot = $this->factoryBot->bots->getNew();

            $id = rand(0, 2);

            if ($user->pagesCount > 10) {
                $id = rand(0, 3);
            }

            switch ($id) {
                case 0:
                    $this->A_BotLikes($bot, $user, 1);
                    break;
                case 2:
                    $this->A_BotPolls($bot, $user, 1);
                    break;
                case 3:
                    $this->A_BotJoin($bot, $user, 1, true);
            }

            sleep(rand(1, 3));
        }
    }


    function A_Test()
    {

        $result = $this->VK->checkUserAccessToken('8f53fbf43655022b2ca583c9a9f64162b357c4b8b2a549cbf8fc60a9a8588d64b44d1d35d4a91c52f005f0');
        var_dump($result);
        die;


        //Получаем активных ботов (кроме про)
        $query = $this->factoryBot->bots->query()->sortFunction('RAND()')->sqlCalcFoundRows(true);
        $query->filter->fieldValue('isActive', '=', true);
        $query->filter->fieldValue('userId', '=', \Config::$adminId); //для тестов! удалить!
        $it = $query->iteratorForSave();

        /**
         * @var Model_Bots_Bot $bot
         */
        foreach ($it as $bot) {

            //Получаем пользователя бота
            $user = $this->factoryUsers->users->getById($bot->userId);

            $result = $this->A_BotPolls($bot, $user, 3);

        }

    }

    /**
     * Актуальный метод бота!
     * @throws \Lib_Exception_InvalidArgument_Backtraced
     * @throws \Lib_Exception_Logic_Backtraced
     */
    public function A_Bot()
    {

        return;

        //Ночью не запускаем бота до 6 утра!
        if (!$this->checkHours()) return;

        clearLogFile('crons/bot.log');

        //для логов
        $tm = time();
        $date = new DateTime();
        $date = $date->format("Y-m-d H:i:s");
        echo "\naction=Bot/Bot:Bot ";
        echo $date;
        $countProBots = 0;
        $countBotsWithoutToken = 0;

        Lib_DB_Factory::Flush();
        Lib_HSocket_Factory::Flush();

        //Получаем всех активных ботов
        $query = $this->factoryBot->bots->query()->sortFunction('RAND()')->sqlCalcFoundRows(true);
        $query->filter->fieldValue('isActive', '=', true);
        //$query->filter->fieldValue('userId', '=', \Config::$adminId); //для тестов!
        $it = $query->iteratorForSave();
        $countBots = count($it);

        $time = time();
        /** @var Model_Bots_Bot $bot */
        foreach ($it as $bot) {

            //для про ботов нужно чтобы обязательно было isBot! ниже см условие..
            if ($bot->isPro and !$bot->isBot) continue;
            if ($bot->isPro) $countProBots++;

            //Получаем пользователя бота
            $user = $this->factoryUsers->users->getById($bot->userId);
            if (!$user) continue;

            // Если у пользователя бота нет access_token то отключаем бота
            if (!$user->access_token) {
                $bot->isActive = false;
                $this->factoryBot->bots->save($bot);
                $countBotsWithoutToken++;
                continue;
            }

            //чекаем токен пользователя
            if (!$this->checkUserAccessToken($user)) {
                usleep(300000);
                continue;
            }


            //проверка токена
//            $result = $this->VK->checkUserAccessToken($user->access_token);
//            if (!empty($result['error']) and $result['error'] == 5) {
//                $this->tokenClear($bot, $user);
//                $bot->isActive = false;
//                $this->factoryBot->bots->save($bot);
//                continue;
//            }

            $this->isTokenClear = false;


            //это нужно! без этого падает подключение к БД почемуто(
            if (time() - $time > 60) {
                $time = time();
                Lib_DB_Factory::Flush();
                Lib_HSocket_Factory::Flush();
            }


            //Получаем задания выполненные пользователем (таблица tasks_users)
            //у которых isActive = true и меняем на false
            //для чего???
            $query = $this->factoryTasks->users->query();
            $query->filter->fieldValue('userId', '=', $user->userId)
                ->fieldValue('isActive', '=', true);
            $ittaskUser = $query->iteratorForSave();

            foreach ($ittaskUser as $taskUser) {
                $taskUser->isActive = false;
                $this->factoryTasks->users->save($taskUser);
            }


            $result = false;
            $bot->makeShadow();

            //перемешаем массив с айдишниками типов заданий для рандомизации выполняемых заданий ботами
            $ids = Model_Config::$ids;
            shuffle($ids);

            //каждым ботом будем выполнять типы заданий по очереди пока не удастся успешно выполнить задание или пока не пройдем все типы заданий!
            foreach ($ids as $id) {

                //для про ботов - выполлняем только включенные пользователем задания!
                //в isBot находится сумма адишников включенных заданий
                //здесь используется хитрая схема с побитовым сравнением ($bot->isBot & $id) - она успешно работает!
                //другое решение - хранить в isBot массив включенных услуг
                if ($bot->isPro and !($bot->isBot & $id)) continue;


                //$id = 2;
                switch ($id) {
                    case 2:
                        $result = $this->A_BotLikes($bot, $user, 3);
                        break;
                    case 4:
                        $result = $this->A_BotReposts($bot, $user, 3);
                        break;
                    case 8:
                        $result = $this->A_BotComments($bot, $user, 3);
                        break;
                    case 16:
                        $result = $this->A_BotJoin($bot, $user, 3);
                        break;
                    case 32:
                        $result = $this->A_BotFriends($bot, $user, 3);
                        break;
                    case 64:
                        $result = $this->A_BotPolls($bot, $user, 3);
                        break;
                }

                // если у пользователя был очищен токен то останавливаем выполнение заданий текущим ботом
                // без токена выполнять задания не получится!
                if ($this->isTokenClear) break;

                //если удалось выполнить хотябы одно задание - выходим из цикла
                if ($result) break;
            }


            //если хотябы одно задание удалось выполнить то в $result вернется true
            if ($result) {
                $bot->lastTask = time();
                $this->factoryBot->bots->save($bot);
            } else {
                $this->countTasksUnsuccessful++;
            }

            usleep(300000);
        }


        Lib_DB_Factory::Flush();
        Lib_HSocket_Factory::Flush();

        echo "\nВсего активных ботов: " . $countBots;
        echo "\nНе работчих токенов у пользователей: " . $this->countWrongUsersTokens;
        echo "\nПремиум ботов: " . $countProBots;
        echo "\nБотов без токенов: " . $countBotsWithoutToken;
        echo "\nОчищено токенов: " . $this->countTokenClear;
        echo "\nОстановлено заданий: " . $this->countStopTasks;
        echo "\nВЫБРАНО ЗАДАНИЙ: " . $this->countTasksForBot;
        echo "\n|лайки: " . $this->countTasksLikes;
        echo "|репосты: " . $this->countTasksReposts;
        echo "|комментарии: " . $this->countTasksComments;
        echo "|подписчики: " . $this->countTasksJoin;
        echo "|друзья: " . $this->countTasksFriends;
        echo "|опросы: " . $this->countTasksPolls;
        echo "\nВЫПОЛНЕНО ЗАДАНИЙ: " . $this->countTasksSuccess;
        echo "\n|лайки: " . $this->countLikesSuccess;
        echo "|репосты: " . $this->countRepostsSuccess;
        echo "|комментарии: " . $this->countCommentsSuccess;
        echo "|подписчики: " . $this->countJoinSuccess;
        echo "|друзья: " . $this->countFriendsSuccess;
        echo "|опросы: " . $this->countPollsSuccess;
        echo "\nОШИБКИ: " . ($this->countErrorsLikes + $this->countErrorsReposts + $this->countErrorsComments + $this->countErrorsJoin + $this->countErrorsFriends + $this->countErrorsPolls);
        echo "\n|лайки: " . $this->countErrorsLikes;
        echo "|репосты: " . $this->countErrorsReposts;
        echo "|комментарии: " . $this->countErrorsComments;
        echo "|подписчики: " . $this->countErrorsJoin;
        echo "|друзья: " . $this->countErrorsFriends;
        echo "|опросы: " . $this->countErrorsPolls;
        echo "\nНе удалось выполнить ни одного задания бота: " . $this->countTasksUnsuccessful;
        echo "\nСработало ограничений по лимитам: " . $this->countCheckLimitsFalse;
        echo "\nВремя выполнения скрипта: " . round((time() - $tm) / 60, 2);
        echo "\n";


    }


    public function A_BotTest()
    {

        Lib_DB_Factory::Flush();
        Lib_HSocket_Factory::Flush();

        //Получаем всех активных ботов
        $query = $this->factoryBot->bots->query()->sortFunction('RAND()')->sqlCalcFoundRows(true);
        $query->filter->fieldValue('isActive', '=', true);
        $query->filter->fieldValue('userId', '=', \Config::$adminId); //для тестов!
        $it = $query->iteratorForSave();


        $time = time();
        /** @var Model_Bots_Bot $bot */
        foreach ($it as $bot) {

            //для про ботов нужно чтобы обязательно было isBot! ниже см условие..
            if ($bot->isPro and !$bot->isBot) continue;
            if ($bot->isPro) $countProBots++;

            //Получаем пользователя бота
            $user = $this->factoryUsers->users->getById($bot->userId);
            if (!$user) continue;

            // Если у пользователя бота нет access_token то отключаем бота
            if (!$user->access_token) {
                $bot->isActive = false;
                $this->factoryBot->bots->save($bot);
                $countBotsWithoutToken++;
                continue;
            }

            //чекаем токен пользователя
            if (!$this->checkUserAccessToken($user)) {
                usleep(300000);
                die;
            }

            //это нужно! без этого падает подключение к БД почемуто(
            if (time() - $time > 60) {
                $time = time();
                Lib_DB_Factory::Flush();
                Lib_HSocket_Factory::Flush();
            }


            //Получаем задания выполненные пользователем (таблица tasks_users)
            //у которых isActive = true и меняем на false
            //для чего???
//            $query = $this->factoryTasks->users->query();
//            $query->filter->fieldValue('userId', '=', $user->userId)
//                ->fieldValue('isActive', '=', true);
//            $ittaskUser = $query->iteratorForSave();
//
//            foreach ($ittaskUser as $taskUser) {
//                $taskUser->isActive = false;
//                $this->factoryTasks->users->save($taskUser);
//            }


            $bot->makeShadow();

//            $result = $this->A_BotJoin($bot, $user, 3, false, true);
//            echo "\n result: " . var_dump($result);

            //получаю нужное задание для тестирования
            $tasks = $this->factoryTasks->tasks->getItemsList($user, 'all', 1, 0, 191366);

            foreach ($tasks as $task){
                $response = $this->VK->groupsJoin($task->ownerId, $user->access_token);
                print_r($user->access_token);
                print_r($task->ownerId);
                print_r($response);
            }
            die;


            usleep(300000);
        }


        Lib_DB_Factory::Flush();
        Lib_HSocket_Factory::Flush();


    }


}
