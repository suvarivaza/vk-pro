<?php

namespace Service\Tasks;

use Service\Users\Model_Users_User;

class Controller_Shell_Tasks_Check extends Controller_Shell
{
    private $_penatly = null;

//    protected $_token = '';

    protected $countNotFindLikes = 0;
    protected $countNotFindReposts = 0;
    protected $countNotFindJoin = 0;
    protected $countNotFindComments = 0;
    protected $countCommentsNotCorrect = 0;
    protected $countNotFindFriends = 0;

    public function __construct()
    {


//        $factory = new \Service\System\Model_Factory();
//        $token = $factory->settings->getByName('token');
//        $this->_token = $token->value;
//        $this->_tokens[] = $token->value;
//
//        $token = $factory->settings->getByName('token2');
//        $this->_tokens[] = $token->value;
//
//        $token = $factory->settings->getByName('token3');
//        $this->_tokens[] = $token->value;
//
//        $token = $factory->settings->getByName('token4');
//        $this->_tokens[] = $token->value;
//
//        $token = $factory->settings->getByName('token5');
//        $this->_tokens[] = $token->value;

        parent::__construct();
    }

    public function A_checkOnce(): void
    {
        $this->_penatly = json_decode(file_get_contents(\Service\Users\Model_Config::$karmaPath), true);
        $this->_penatly = $this->_penatly['penatly'];

        $query = $this->factoryTasks->users->query();
        $query->filter->fieldValue('isDone', '=', true);
        $it = $query->iterator();

        $list = [];

        /** @var Model_Users_User $taskUser */
        foreach ($it as $taskUser) {
            if (!$taskUser->uid) {
                continue;
            }

            $list[$taskUser->taskId][] = $taskUser;
        }

        foreach ($list as $taskId => $taskUsers) {
            $task = $this->factoryTasks->tasks->getById($taskId);

            if ($task === null) {
                continue;
            }

            if ($task->isDel) {
                continue;
            }

            if ($task->dateCreate < strtotime('-6 MONTH')) { // Проверяем шесть месяцев
                continue;
            }

            if ($task->type === 'views' || $task->type === 'video') { // Просмотры мы не проверим
                continue;
            }

            switch ($task->type) {
                case 'likes':
                    $this->_checkLikes($task, $taskUsers);
                    break;
                case 'reposts':
                    $this->_checkReposts($task, $taskUsers);
                    break;
                case 'join':
                    $this->_checkJoin($task, $taskUsers);
                    break;
                case 'comments':
                    $this->_checkComments($task, $taskUsers);
                    break;
                case 'friends':
                    $this->_checkFriends($task, $taskUsers);
                    break;
            }
        }
    }

    /**
     * работает - проверено!
     * @param Model_Tasks_Task $task
     * @param Model_Users_User[] $taskUsers
     */
    protected function _checkLikes(Model_Tasks_Task $task, $taskUsers)
    {

        $user = $this->factoryUsers->users->getById($task->userId);

        if ($user === null) {
            return;
        }

        $offset = 0;
        $uids = [];
        $taskUsersCheck = $taskUsers;

        do {
            $count = 0;

            $item_id = $task->vkType == 'comment' ? $task->commentId : $task->itemId;
            $response = $this->VK->getLikes($task->vkType, $task->ownerId, $task->ownerType, $item_id, $this->getRandomCheckToken(), 1000, $offset);

            if ($response['error'] > 0 || !isset($response['count'])) {
                usleep(300000);
                continue;
            }

            if (!$count) {
                $count = $response['count'];
            }


            //формируем массив с айдишниками пользователей поставивших лайк
            $uids = array_merge($uids, $response['items']);

            foreach ($taskUsersCheck as $id => $taskUser) {
                if (in_array($taskUser->uid, $uids)) {
                    unset($taskUsersCheck[$id]);
                }
            }

            if (!count($taskUsersCheck)) {
                break;
            }

            $offset += 1000;

            usleep(300000);

        } while ($count > $offset);

        if (!$uids) die;

        /** @var \Service\Tasks\Model_Users_User $taskUser */
        foreach ($taskUsers as $taskUser) {

            if (!in_array($taskUser->uid, $uids)) {

                $this->countNotFindLikes++;

                /** @var Model_Users_User $user */
                $user = $this->factoryUsers->users->getById($taskUser->userId, true);

                $karma = $this->factoryUsers->users->karma->getNew();
                $karma->userId = $user->userId;
                $karma->dateCreate = time();
                $karma->karma = -(float)$this->_penatly[$task->type];
                $karma->karmaFrom = $user->karma;
                $user->karma -= $this->_penatly[$task->type];
                $karma->taskId = $task->taskId;
                $karma->karmaTo = $user->karma;
                $karma->comment = 'Снял лайк';
                $this->factoryUsers->users->karma->save($karma);

                $balance = $this->factoryUsers->users->balance->getNew();
                $balance->userId = $user->userId;
                $balance->isPenalty = true;
                $balance->balance = -(float)$task->price;
                $balance->balanceFrom = $user->balance;
                $user->balance += $balance->balance;
                $balance->balanceTo = $user->balance;
                $balance->dateCreate = time();
                $balance->comment = 'Снял лайк';
                $this->factoryUsers->users->balance->save($balance);

                $this->factoryUsers->users->save($user);

                $taskUser->makeShadow();
                $taskUser->isDone = false;
                $taskUser->isDel = true;
                $taskUser->isDelDate = time();
                $this->factoryTasks->users->save($taskUser);

                /** @var Model_Users_User $author */
                $author = $this->factoryUsers->users->getById($task->userId, true);
                $balance = $this->factoryUsers->users->balance->getNew();
                $balance->userId = $author->userId;
                $balance->isCompensation = true;
                $balance->balance = (float)$task->price;
                $balance->balanceFrom = $author->balance;
                $author->balance += $balance->balance;
                $balance->balanceTo = $author->balance;
                $balance->dateCreate = time();
                $balance->comment = 'Компенсация за снятый лайк';
                $this->factoryUsers->users->balance->save($balance);
                $this->factoryUsers->users->save($author);

                $task->makeShadow();

                if ($taskUser->isBot) {
                    $task->countReadyBot--;
                }
                $task->countReady--;
                $task->countRemain = $task->count - $task->countReady;
                $this->factoryTasks->tasks->save($task);
            }
        }
    }

    /*
     * не раоботает!!!
     */
    protected function _checkReposts(Model_Tasks_Task $task, $taskUsers): void
    {

        /** @var \Service\Tasks\Model_Users_User $taskUser */
        foreach ($taskUsers as $taskUser) {

            /** @var Model_Users_User $user */
            $user = $this->factoryUsers->users->getById($taskUser->userId, true);

            $response = $this->VK->isLiked($taskUser->uid, $task->ownerId, $task->itemId, $task->vkType, $task->ownerType, $this->getRandomCheckToken());

            if (!empty($response['error'])) {
                usleep(300000);
                continue;
            }

            //если репост не сделан
            if ($response['copied'] == 0) {

                $this->countNotFindReposts++;
                $karma = $this->factoryUsers->users->karma->getNew();
                $karma->userId = $user->userId;
                $karma->dateCreate = time();
                $karma->karma = -(float)$this->_penatly[$task->type];
                $karma->karmaFrom = $user->karma;
                $user->karma -= $this->_penatly[$task->type];
                $karma->taskId = $task->taskId;
                $karma->karmaTo = $user->karma;
                $karma->comment = 'Удалил репост';
                $this->factoryUsers->users->karma->save($karma);

                $balance = $this->factoryUsers->users->balance->getNew();
                $balance->userId = $user->userId;
                $balance->isPenalty = true;
                $balance->balance = -(float)$task->price;
                $balance->balanceFrom = $user->balance;
                $user->balance += $balance->balance;
                $balance->balanceTo = $user->balance;
                $balance->dateCreate = time();
                $balance->comment = 'Удалил репост';
                $this->factoryUsers->users->balance->save($balance);

                $this->factoryUsers->users->save($user);

                $taskUser->makeShadow();
                $taskUser->isDone = false;
                $taskUser->isDel = true;
                $taskUser->isDelDate = time();
                $this->factoryTasks->users->save($taskUser);

                /** @var Model_Users_User $author */
                $author = $this->factoryUsers->users->getById($task->userId, true);
                $balance = $this->factoryUsers->users->balance->getNew();
                $balance->userId = $author->userId;
                $balance->isCompensation = true;
                $balance->balance = (float)$task->price;
                $balance->balanceFrom = $author->balance;
                $author->balance += $balance->balance;
                $balance->balanceTo = $author->balance;
                $balance->dateCreate = time();
                $balance->comment = 'Компенсация за снятый репост';
                $this->factoryUsers->users->balance->save($balance);
                $this->factoryUsers->users->save($author);

                $task->makeShadow();

                if ($taskUser->isBot) {
                    $task->countReadyBot--;
                }
                $task->countReady--;
                $task->countRemain = $task->count - $task->countReady;
                $this->factoryTasks->tasks->save($task);
            }
        }
    }

    /*
     * не работает!!!
     */
    public function _checkJoin(Model_Tasks_Task $task, $taskUsers)
    {

        return;

        /** @var \Service\Tasks\Model_Users_User $taskUser */
        $uids = [];
        $tasks = [];

        $user = $this->factoryUsers->users->getById($task->userId);

        foreach ($taskUsers as $taskUser) {
            $tasks[$taskUser->uid] = $taskUser;
            $uids[] = $taskUser->uid;
        }

        $offet = 0;
        $limit = 500;

        while ($offet < count($uids)) {

            $user_ids = implode(',', array_slice($uids, $offet, $limit));
            $response = $this->VK->isMembers($task->ownerId, $user_ids, $this->getRandomCheckToken());

            if (!empty($response['error'])) {
                usleep(300000);
                continue;
            }

            $offet += $limit;
            foreach ($response as $id => $row) {

                if ($id === 'error') continue;

                if (!$row['member']) {

                    $this->countNotFindJoin++;
                    /** @var Model_Users_User $user */
                    $user = $this->factoryUsers->users->getByUid($row['user_id'], true);
                    $karma = $this->factoryUsers->users->karma->getNew();
                    $karma->userId = $user->userId;
                    $karma->dateCreate = time();
                    $karma->karma = -(float)$this->_penatly[$task->type];
                    $karma->karmaFrom = $user->karma;
                    $user->karma -= $this->_penatly[$task->type];
                    $karma->taskId = $task->taskId;
                    $karma->karmaTo = $user->karma;
                    $karma->comment = 'Отписался от группы';
                    $this->factoryUsers->users->karma->save($karma);

                    $balance = $this->factoryUsers->users->balance->getNew();
                    $balance->userId = $user->userId;
                    $balance->isPenalty = true;
                    $balance->balance = -(float)$task->price;
                    $balance->balanceFrom = $user->balance;
                    $user->balance += $balance->balance;
                    $balance->balanceTo = $user->balance;
                    $balance->dateCreate = time();
                    $balance->comment = 'Отписался от группы';
                    $this->factoryUsers->users->balance->save($balance);

                    $this->factoryUsers->users->save($user);

                    $taskUser = $tasks[$row['user_id']];

                    $taskUser->makeShadow();
                    $taskUser->isDone = false;
                    $taskUser->isDel = true;
                    $taskUser->isDelDate = time();
                    $this->factoryTasks->users->save($taskUser);

                    /** @var Model_Users_User $author */
                    $author = $this->factoryUsers->users->getById($task->userId, true);
                    $balance = $this->factoryUsers->users->balance->getNew();
                    $balance->userId = $author->userId;
                    $balance->isCompensation = true;
                    $balance->balance = (float)$task->price;
                    $balance->balanceFrom = $author->balance;
                    $author->balance += $balance->balance;
                    $balance->balanceTo = $author->balance;
                    $balance->dateCreate = time();
                    $balance->comment = 'Компенсация за отписку от группы';
                    $this->factoryUsers->users->balance->save($balance);
                    $this->factoryUsers->users->save($author);

                    $task->makeShadow();

                    if ($taskUser->isBot) {
                        $task->countReadyBot--;
                    }
                    $task->countReady--;
                    $task->countRemain = $task->count - $task->countReady;
                    $this->factoryTasks->tasks->save($task);
                }
            }
        }
    }

    protected function _checkComments(Model_Tasks_Task $task, $taskUsers)
    {

        $comments = $task->getComments();
        $arr = \Service\Posting\Model_Config::GetEmoji();

        foreach ($comments as $id => $comment) {
            $comments[$id] = strtr($comment, $arr[0]['old']);
        }

        $black = explode(' ', file_get_contents(ENGINE_PATH . 'static/black.txt'));

        $count = 100;
        $offset = 0;
        $items = [];
        $taskUsersCheck = $taskUsers;
        do {

            switch ($task->vkType) {
                case 'post':
                    $response = $this->VK->getPostComments($task->ownerId, $task->itemId, $task->ownerType, $this->getRandomCheckToken(), $count, $offset);
                    break;
                case 'photo':
                    $response = $this->VK->getPhotoComments($task->ownerId, $task->itemId, $task->ownerType, $this->getRandomCheckToken(), $count, $offset);
                    break;
                case 'video':
                    $response = $this->VK->getVideoComments($task->ownerId, $task->itemId, $task->ownerType, $this->getRandomCheckToken(), $count, $offset);
                    break;
            }

            if (!empty($response['error'])) {
                usleep(300000);
                continue;
            }

            if (isset($response['items']) and !is_array($response['items'])) {
                $response['count'] = 100000;
                continue;
            }

            $items = array_merge($items, $response['items']);

            foreach ($taskUsersCheck as $id => $taskUser) {
                foreach ($items as $item) {
                    if ($item['from_id'] == $taskUser->uid) {
                        unset($taskUsersCheck[$id]);
                    }
                }
            }

            if (!count($taskUsersCheck)) {
                break;
            }
            $offset += $count;
            usleep(300000);
        } while ($response['count'] > $offset);

        /** @var \Service\Tasks\Model_Users_User $taskUser */
        foreach ($taskUsers as $taskUser) {
            $currentComment = null;

            foreach ($items as $item) {
                if ($item['from_id'] == $taskUser->uid) {
                    $currentComment = $item;
                }
            }

            if ($currentComment === null) {
                $this->countNotFindComments++;
                /** @var Model_Users_User $user */
                $user = $this->factoryUsers->users->getById($taskUser->userId, true);
                $karma = $this->factoryUsers->users->karma->getNew();
                $karma->userId = $user->userId;
                $karma->dateCreate = time();
                $karma->karma = -(float)$this->_penatly[$task->type];
                $karma->karmaFrom = $user->karma;
                $user->karma -= $this->_penatly[$task->type];
                $karma->taskId = $task->taskId;
                $karma->karmaTo = $user->karma;
                $karma->comment = 'Удалил комментарий';
                $this->factoryUsers->users->karma->save($karma);

                $balance = $this->factoryUsers->users->balance->getNew();
                $balance->userId = $user->userId;
                $balance->isPenalty = true;
                $balance->balance = -(float)$task->price;
                $balance->balanceFrom = $user->balance;
                $user->balance += $balance->balance;
                $balance->balanceTo = $user->balance;
                $balance->dateCreate = time();
                $balance->comment = 'Удалил комментарий';
                $this->factoryUsers->users->balance->save($balance);

                $this->factoryUsers->users->save($user);

                $taskUser->makeShadow();
                $taskUser->isDone = false;
                $taskUser->isDel = true;
                $taskUser->isDelDate = time();
                $this->factoryTasks->users->save($taskUser);

                /** @var Model_Users_User $author */
                $author = $this->factoryUsers->users->getById($task->userId, true);
                $balance = $this->factoryUsers->users->balance->getNew();
                $balance->userId = $author->userId;
                $balance->isCompensation = true;
                $balance->balance = (float)$task->price;
                $balance->balanceFrom = $author->balance;
                $author->balance += $balance->balance;
                $balance->balanceTo = $author->balance;
                $balance->dateCreate = time();
                $balance->comment = 'Компенсация за удаленный комментарий';
                $this->factoryUsers->users->balance->save($balance);
                $this->factoryUsers->users->save($author);

                $task->makeShadow();

                if ($taskUser->isBot) {
                    $task->countReadyBot--;
                }
                $task->countReady--;
                $task->countRemain = $task->count - $task->countReady;
                $this->factoryTasks->tasks->save($task);

                continue;
            }

            if ($task->commentType === 3) {
                if (!in_array($currentComment['text'], $comments) || false) {
                    $this->countCommentsNotCorrect++;
                    $user = $this->factoryUsers->users->getById($taskUser->userId, true);
                    $karma = $this->factoryUsers->users->karma->getNew();
                    $karma->userId = $user->userId;
                    $karma->dateCreate = time();
                    $karma->karma = -(float)$this->_penatly[$task->type];
                    $karma->taskId = $task->taskId;
                    $karma->karmaFrom = $user->karma;
                    $user->karma -= $this->_penatly[$task->type];
                    $karma->karmaTo = $user->karma;
                    $karma->comment = 'Комментарий не правильный';
                    $this->factoryUsers->users->karma->save($karma);

                    $balance = $this->factoryUsers->users->balance->getNew();
                    $balance->userId = $user->userId;
                    $balance->isPenalty = true;
                    $balance->balance = -(float)$task->price;
                    $balance->balanceFrom = $user->balance;
                    $user->balance += $balance->balance;
                    $balance->balanceTo = $user->balance;
                    $balance->dateCreate = time();
                    $balance->comment = 'Комментарий не правильный';
                    $this->factoryUsers->users->balance->save($balance);

                    $this->factoryUsers->users->save($user);

                    $taskUser->makeShadow();
                    $taskUser->isDone = false;
                    $taskUser->isDel = true;
                    $taskUser->isDelDate = time();
                    $this->factoryTasks->users->save($taskUser);

                    $author = $this->factoryUsers->users->getById($task->userId, true);
                    $balance = $this->factoryUsers->users->balance->getNew();
                    $balance->userId = $author->userId;
                    $balance->isCompensation = true;
                    $balance->balance = (float)$task->price;
                    $balance->balanceFrom = $author->balance;
                    $author->balance += $balance->balance;
                    $balance->balanceTo = $author->balance;
                    $balance->dateCreate = time();
                    $balance->comment = 'Компенсация за удаленный комментарий';
                    $this->factoryUsers->users->balance->save($balance);
                    $this->factoryUsers->users->save($author);

                    $task->makeShadow();

                    if ($taskUser->isBot) {
                        $task->countReadyBot--;
                    }
                    $task->countReady--;
                    $task->countRemain = $task->count - $task->countReady;
                    $this->factoryTasks->tasks->save($task);
                }
            } elseif ($task->commentType == 2) {
                //тут что???
            } else {
                $words = explode(' ', $currentComment['text']);
                $found = false;

                foreach ($words as $word) {
                    if (in_array($word, $black)) {
                        $found = true;
                    }
                }

                if ($found) {
                    $this->countCommentsNotCorrect++;
                    $user = $this->factoryUsers->users->getById($taskUser->userId, true);
                    $karma = $this->factoryUsers->users->karma->getNew();
                    $karma->userId = $user->userId;
                    $karma->dateCreate = time();
                    $karma->karma = -(float)$this->_penatly[$task->type];
                    $karma->karmaFrom = $user->karma;
                    $user->karma -= $this->_penatly[$task->type];
                    $karma->taskId = $task->taskId;
                    $karma->karmaTo = $user->karma;
                    $karma->comment = 'Комментарий не правильный';
                    $this->factoryUsers->users->karma->save($karma);

                    $balance = $this->factoryUsers->users->balance->getNew();
                    $balance->userId = $user->userId;
                    $balance->isPenalty = true;
                    $balance->balance = -(float)$task->price;
                    $balance->balanceFrom = $user->balance;
                    $user->balance += $balance->balance;
                    $balance->balanceTo = $user->balance;
                    $balance->dateCreate = time();
                    $balance->comment = 'Комментарий не правильный';
                    $this->factoryUsers->users->balance->save($balance);

                    $this->factoryUsers->users->save($user);

                    $taskUser->makeShadow();
                    $taskUser->isDone = false;
                    $taskUser->isDel = true;
                    $taskUser->isDelDate = time();
                    $this->factoryTasks->users->save($taskUser);

                    $author = $this->factoryUsers->users->getById($task->userId, true);
                    $balance = $this->factoryUsers->users->balance->getNew();
                    $balance->userId = $author->userId;
                    $balance->isCompensation = true;
                    $balance->balance = (float)$task->price;
                    $balance->balanceFrom = $author->balance;
                    $author->balance += $balance->balance;
                    $balance->balanceTo = $author->balance;
                    $balance->dateCreate = time();
                    $balance->comment = 'Компенсация за удаленный комментарий';
                    $this->factoryUsers->users->balance->save($balance);
                    $this->factoryUsers->users->save($author);

                    $task->makeShadow();

                    if ($taskUser->isBot) {
                        $task->countReadyBot--;
                    }
                    $task->countReady--;
                    $task->countRemain = $task->count - $task->countReady;
                    $this->factoryTasks->tasks->save($task);
                }
            }
        }
    }

    public function _checkFriends(Model_Tasks_Task $task, $taskUsers): void
    {
        $offset = 0;
        $uids = [];
        $user = $this->factoryUsers->users->getById($task->userId);

        do {

            //получаем подписчиков
            $response = $this->VK->getFollowers($task->ownerId, $offset, $this->getRandomCheckToken());

            if ($response['error'] > 0) {
                usleep(300000);
                continue;
            }

            foreach ($response['items'] as $item) {
                $uids[] = $item;
            }

            $offset += 1000;
            usleep(300000);
        } while ($response['count'] > $offset);

        //и теперь еще получаем друзей??? и сливаем подписчиков и друзей в один массив???
        $response = $this->VK->getFriends($task->ownerId, $this->getRandomCheckToken());

        foreach ($response['items'] as $item) {
            $uids[] = $item;
        }

        /** @var \Service\Tasks\Model_Users_User $taskUser */
        foreach ($taskUsers as $taskUser) {

            if (!in_array($taskUser->uid, $uids)) {
                $this->countNotFindFriends++;
                /** @var Model_Users_User $user */
                $user = $this->factoryUsers->users->getById($taskUser->userId, true);
                $karma = $this->factoryUsers->users->karma->getNew();
                $karma->userId = $user->userId;
                $karma->dateCreate = time();
                $karma->karma = -(float)$this->_penatly[$task->type];
                $karma->karmaFrom = $user->karma;
                $karma->taskId = $task->taskId;
                $user->karma -= $this->_penatly[$task->type];
                $karma->karmaTo = $user->karma;
                $karma->comment = 'Снял заявку в друзья';
                $this->factoryUsers->users->karma->save($karma);

                $balance = $this->factoryUsers->users->balance->getNew();
                $balance->userId = $user->userId;
                $balance->isPenalty = true;
                $balance->balance = -(float)$task->price;
                $balance->balanceFrom = $user->balance;
                $user->balance += $balance->balance;
                $balance->balanceTo = $user->balance;
                $balance->dateCreate = time();
                $balance->comment = 'Снял заявку в друзья';
                $this->factoryUsers->users->balance->save($balance);

                $this->factoryUsers->users->save($user);

                $taskUser->makeShadow();
                $taskUser->isDone = false;
                $taskUser->isDel = true;
                $taskUser->isDelDate = time();
                $this->factoryTasks->users->save($taskUser);

                /** @var Model_Users_User $author */
                $author = $this->factoryUsers->users->getById($task->userId, true);
                $balance = $this->factoryUsers->users->balance->getNew();
                $balance->userId = $author->userId;
                $balance->isCompensation = true;
                $balance->balance = (float)$task->price;
                $balance->balanceFrom = $author->balance;
                $author->balance += $balance->balance;
                $balance->balanceTo = $author->balance;
                $balance->dateCreate = time();
                $balance->comment = 'Компенсация за снятие заявки в друзья';
                $this->factoryUsers->users->balance->save($balance);
                $this->factoryUsers->users->save($author);

                $task->makeShadow();

                if ($taskUser->isBot) {
                    $task->countReadyBot--;
                }
                $task->countReady--;
                $task->countRemain = $task->count - $task->countReady;
                $this->factoryTasks->tasks->save($task);
            }
        }

        return;
    }

    public function A_check5Min(): void
    {

        //Установим время и дату для записи в лог скрипта
        $tm = time();
        $date = new \DateTime();
        $date = $date->format("Y-m-d H:i:s");

        $this->_penatly = json_decode(file_get_contents(\Service\Users\Model_Config::$karmaPath), true);
        $this->_penatly = $this->_penatly['penatly'];

        //получаем задания выполненные пользователями (или ботом)
        //берем задания выполненные между 11 и 5 минут назад (т.е. проверяем прошлую пятиминутку)
        $query = $this->factoryTasks->users->query();
        $query->filter
            ->fieldValue('isDone', '=', true)
            ->fieldValue('isDoneDate', '<', time() - (5 * 60))
            ->fieldValue('isDoneDate', '>', time() - (11 * 60))
            //->fieldValue('type', '=', 'join') //чекаем только подписчиков???
            //->fieldValue('userId', '=', \Config::$adminId) //для тестов
            ->fieldCollection('type', 'NOT IN', ['views', 'video']); //не чекаем просмотры постов и просмотры видео

        $it = $query->iterator();

        $countTasks = count($it);

        $list = [];

        /** @var Model_Users_User $taskUser */
        foreach ($it as $taskUser) {

            if (!$taskUser->uid) {
                continue;
            }

            $list[$taskUser->taskId][] = $taskUser;
        }

        foreach ($list as $taskId => $taskUsers) {

            $task = $this->factoryTasks->tasks->getById($taskId);

            if ($task === null) {
                continue;
            }

            //пропускаем удаленные задания
            if ($task->isDel) {
                continue;
            }

            if ($task->dateCreate < strtotime('-3 MONTH')) { // Проверяем шесть месяцев
                continue;
            }

            if ($task->type === 'views' || $task->type === 'video') { // Просмотры мы не проверим
                continue;
            }


            switch ($task->type) {
                case 'likes':
                    $this->_checkLikes($task, $taskUsers);
                    break;
                case 'reposts':
                    $this->_checkReposts($task, $taskUsers);
                    break;
                case 'join':
                    $this->_checkJoin($task, $taskUsers);
                    break;
                case 'comments':
                    $this->_checkComments($task, $taskUsers);
                    break;
                case 'friends':
                    $this->_checkFriends($task, $taskUsers);
                    break;
            }
        }

        if ($countTasks) {
            echo "\naction=Tasks/Tasks/Check:check5Min";
            echo $date;
            echo "\nВремя выполнения: " . round((time() - $tm) / 60, 2);
            echo "\nЗаданий для проверки: " . $countTasks;
            echo "\nСнял лайк: " . $this->countNotFindLikes;
            echo "\nУдалил репост: " . $this->countNotFindReposts;
            echo "\nОтписался от группы: " . $this->countNotFindJoin;
            echo "\nУдалил комментарий: " . $this->countNotFindComments;
            echo "\nКомментарий не правильный: " . $this->countCommentsNotCorrect;
            echo "\nСнял заявку в друзья: " . $this->countNotFindFriends;
            echo "\n";
        }


    }

    public function A_checkHour(): void
    {
        //Установим время и дату для записи в лог скрипта
        $tm = time();
        $date = new \DateTime();
        $date = $date->format("Y-m-d H:i:s");

        $this->_penatly = json_decode(file_get_contents(\Service\Users\Model_Config::$karmaPath), true);
        $this->_penatly = $this->_penatly['penatly'];

        $query = $this->factoryTasks->users->query();
        $query->filter
            ->fieldValue('isDone', '=', true)
            ->fieldValue('isDoneDate', '<', time() - (1 * 60 * 60))
            ->fieldValue('isDoneDate', '>', time() - (2 * 60 * 60))
            //->fieldValue('type', '=', 'join')
            ->fieldCollection('type', 'NOT IN', ['views', 'video']);
        $it = $query->iterator();

        $countTasks = count($it);
        $list = [];

        /** @var Model_Users_User $taskUser */
        foreach ($it as $taskUser) {
            $list[$taskUser->taskId][] = $taskUser;
        }

        foreach ($list as $taskId => $taskUsers) {

            $task = $this->factoryTasks->tasks->getById($taskId);

            if ($task === null) {
                continue;
            }

            if ($task->isDel) {
                continue;
            }

            if ($task->dateCreate < strtotime('-3 MONTH')) { // Проверяем шесть месяцев
                continue;
            }

            switch ($task->type) {
                case 'likes':
                    $this->_checkLikes($task, $taskUsers);
                    break;
                case 'reposts':
                    $this->_checkReposts($task, $taskUsers);
                    break;
                case 'join':
                    $this->_checkJoin($task, $taskUsers);
                    break;
                case 'comments':
                    $this->_checkComments($task, $taskUsers);
                    break;
                case 'friends':
                    $this->_checkFriends($task, $taskUsers);
                    break;
            }
        }

        if ($countTasks) {
            echo "\naction=Tasks/Tasks/Check:checkHour";
            echo $date;
            echo "\nВремя выполнения: " . round((time() - $tm) / 60, 2);
            echo "\nЗаданий для проверки: " . $countTasks;
            echo "\nСнял лайк: " . $this->countNotFindLikes;
            echo "\nУдалил репост: " . $this->countNotFindReposts;
            echo "\nОтписался от группы: " . $this->countNotFindJoin;
            echo "\nУдалил комментарий: " . $this->countNotFindComments;
            echo "\nКомментарий не правильный: " . $this->countCommentsNotCorrect;
            echo "\nСнял заявку в друзья: " . $this->countNotFindFriends;
            echo "\n";
        }

    }

    public function A_checkDay(): void
    {
        //Установим время и дату для записи в лог скрипта
        $tm = time();
        $date = new \DateTime();
        $date = $date->format("Y-m-d H:i:s");

        $this->_penatly = json_decode(file_get_contents(\Service\Users\Model_Config::$karmaPath), true);
        $this->_penatly = $this->_penatly['penatly'];

        $query = $this->factoryTasks->users->query();
        $query->filter
            ->fieldValue('isDone', '=', true)
            ->fieldValue('isDoneDate', '>', strtotime(date('d.m.Y')))
            //->fieldValue('type', '=', 'join')
            ->fieldCollection('type', 'NOT IN', ['views', 'video']);
        $it = $query->iterator();

        $countTasks = count($it);

        $list = [];

        /** @var Model_Users_User $taskUser */
        foreach ($it as $taskUser) {
            $list[$taskUser->taskId][] = $taskUser;
        }

        foreach ($list as $taskId => $taskUsers) {
            $task = $this->factoryTasks->tasks->getById($taskId);

            if ($task === null) {
                continue;
            }

            if ($task->isDel) {
                continue;
            }

            if ($task->dateCreate < strtotime('-3 MONTH')) { // Проверяем шесть месяцев
                continue;
            }

            switch ($task->type) {
                case 'likes':
                    $this->_checkLikes($task, $taskUsers);
                    break;
                case 'reposts':
                    //$this->_checkReposts( $task, $taskUsers );
                    break;
                case 'join':
                    $this->_checkJoin($task, $taskUsers);
                    break;
                case 'comments':
                    $this->_checkComments($task, $taskUsers);
                    break;
                case 'friends':
                    $this->_checkFriends($task, $taskUsers);
                    break;
            }
        }

        if ($countTasks) {
            echo "\naction=Tasks/Tasks/Check:checkDay";
            echo $date;
            echo "\nВремя выполнения: " . round((time() - $tm) / 60, 2);
            echo "\nЗаданий для проверки: " . $countTasks;
            echo "\nСнял лайк: " . $this->countNotFindLikes;
            echo "\nУдалил репост: " . $this->countNotFindReposts;
            echo "\nОтписался от группы: " . $this->countNotFindJoin;
            echo "\nУдалил комментарий: " . $this->countNotFindComments;
            echo "\nКомментарий не правильный: " . $this->countCommentsNotCorrect;
            echo "\nСнял заявку в друзья: " . $this->countNotFindFriends;
            echo "\n";
        }

    }

    public function A_checkMonth(): void
    {

        //Установим время и дату для записи в лог скрипта
        $tm = time();
        $date = new \DateTime();
        $date = $date->format("Y-m-d H:i:s");

        $this->_penatly = json_decode(file_get_contents(\Service\Users\Model_Config::$karmaPath), true);
        $this->_penatly = $this->_penatly['penatly'];

        $query = $this->factoryTasks->users->query();
        $query->filter
            ->fieldValue('isDone', '=', true)
            ->fieldValue('isDoneDate', '>', strtotime('-1 MONTH'))
            ->fieldValue('isDoneDate', '<', strtotime('-1 MONTH') + 86400)
            //->fieldValue('type', '=', 'join')
            ->fieldCollection('type', 'NOT IN', ['views', 'video']);

        $it = $query->iterator();

        $countTasks = count($it);

        $list = [];

        /** @var Model_Users_User $taskUser */
        foreach ($it as $taskUser) {
            $list[$taskUser->taskId][] = $taskUser;
        }

        foreach ($list as $taskId => $taskUsers) {
            $task = $this->factoryTasks->tasks->getById($taskId);

            if ($task === null) {
                continue;
            }

            if ($task->isDel) {
                continue;
            }

            if ($task->dateCreate < strtotime('-3 MONTH')) { // Проверяем шесть месяцев
                continue;
            }

            switch ($task->type) {
                case 'likes':
                    $this->_checkLikes($task, $taskUsers);
                    break;
                case 'join':
                    $this->_checkJoin($task, $taskUsers);
                    break;
                case 'comments':
                    $this->_checkComments($task, $taskUsers);
                    break;
                case 'friends':
                    $this->_checkFriends($task, $taskUsers);
                    break;
            }
        }

        if ($countTasks) {
            echo "\naction=Tasks/Tasks/Check:checkMonth";
            echo $date;
            echo "\nВремя выполнения: " . round((time() - $tm) / 60, 2);
            echo "\nЗаданий для проверки: " . $countTasks;
            echo "\nСнял лайк: " . $this->countNotFindLikes;
            echo "\nУдалил репост: " . $this->countNotFindReposts;
            echo "\nОтписался от группы: " . $this->countNotFindJoin;
            echo "\nУдалил комментарий: " . $this->countNotFindComments;
            echo "\nКомментарий не правильный: " . $this->countCommentsNotCorrect;
            echo "\nСнял заявку в друзья: " . $this->countNotFindFriends;
            echo "\n";
        }


    }


    /*
     * Проверка доступност заданий для выполнения
     * если задание не доступно то останавливаем
     */
    public function A_CheckValidTasks()
    {

        //Установим время и дату для записи в лог скрипта
        $tm = time();
        $date = new \DateTime();
        $date = $date->format("Y-m-d H:i:s");

        $types = [
            'likes',
            'reposts',
            'comments',
            'polls',
            'views',
            'join',
            'friends',
            'video',
        ];


        $countTasks = 0;
        foreach ($types as $type) {

            $query = $this->factoryTasks->tasks->query()->sqlCalcFoundRows(true)
                ->sort('taskId', 'ASC');
            $query->filter->fieldValue('type', '=', $type);
            $query->filter->fieldValue('isDel', '=', false);
            $query->filter->fieldValue('active', '=', true);
            $query->filter->fieldValue('countRemain', '>', 0);
            $it = $query->iterator();
            $total = $it->getTotal();
            $countTasks += $total;

            /**
             * @var Model_Tasks_Task $task
             */
            foreach ($it as $task) {

                if (in_array($task->type, ['likes', 'reposts', 'comments', 'polls', 'views']) and $task->vkType != 'comment') {

                    $response = $this->VK->getPost($task->ownerId, $task->itemId, $task->ownerType, $this->getRandomCheckToken());
                    $time = microtime(true);

                    if (!empty($response['error']) or $response === []) {
                        $reason = 'Пост недоступен';
                        $this->taskStop($task, $reason);
                        echo "\n" . $this->countStopTasks . ' ' . $reason;
                    }

                } else if ($task->type == 'join') {

                    $response = $this->VK->getGroup($task->ownerId, $this->getRandomCheckToken());
                    $time = microtime(true);

                    if (!empty($response['error'])) {
                        $reason = 'Группа недоступна';
                    } else {

                        if (!empty($response[0]['is_closed']) and $response[0]['is_closed'] == 2) {
                            $reason = 'Это частная группа!';
                        }
                        else if ($response[0]['deactivated'] == 'banned') {
                            $reason = 'Группа забанена!';
                        } else if ($response[0]['deactivated'] == 'deleted') {
                            $reason = 'Группа удалена!';
                        } else if ($response[0]['type'] == 'event') {
                            $reason = 'Это мероприятие!';
                        }

                    }

                    if(!empty($reason)){
                        $this->taskStop($task, $reason);
                        echo "\n" . $this->countStopTasks . ' ' . $reason;
                    }

                } else if ($task->type == 'friends') {

                    $response = $this->VK->getUser($task->ownerId, $this->getRandomCheckToken());
                    $time = microtime(true);

                    if (!$response or (!empty($response[0]['deactivated'])) or (!empty($response['error']))) {
                        $reason = 'Пользователь недоступен';
                        $this->taskStop($task, $reason);
                        echo "\n" . $this->countStopTasks . ' ' . $reason;
                    }

                } else if ($task->type == 'video') {

                    $response = $this->VK->getVideo($task->ownerId, $task->itemId, $task->ownerType, $this->getRandomCheckToken());
                    $time = microtime(true);

                    if (!$response or empty($response['items']) or (!empty($response['error']))) {
                        $reason = 'Видео недоступно';
                        $this->taskStop($task, $reason);
                        echo "\n" . $this->countStopTasks . ' ' . $reason;
                    }

                }

                if((microtime(true) - $time) < 300000) usleep(300000);
            }

        }


        if ($this->countStopTasks) {
            echo "\naction=Tasks/Tasks/Check:CheckValidTasks";
            echo $date;
            echo "\nВремя выполнения: " . round((time() - $tm) / 60, 2);
            echo "\nЗаданий проверено: " . $countTasks;
            echo "\nОстановлено заданий: " . $this->countStopTasks;
            echo "\n";
        }

    }

    /*
     * служебный метод
     * перебором обновляем данные во всех заданиях
     */
    private function A_UpdateTasks()
    {

        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        //Установим время и дату для записи в лог скрипта
        $tm = time();
        $date = new \DateTime();
        $date = $date->format("Y-m-d H:i:s");

        $types = [
            'likes',
            'reposts',
            'comments',
            'polls',
            'views',
            'join',
            'friends',
            'video',
        ];


        $countTasks = 0;
        $countUpdatedTasks = 0;


        foreach ($types as $type) {

            $query = $this->factoryTasks->tasks->query()->sqlCalcFoundRows(true)
                ->sort('taskId', 'ASC');
            $query->filter->fieldValue('type', '=', $type);
            //$query->filter->fieldValue('isDel', '=', false);
            $query->filter->fieldValue('active', '=', true);
            //$query->filter->fieldValue('countRemain', '>', 0);
            $it = $query->iterator();
            $total = $it->getTotal();

            $countTasks += $total;

            foreach ($it as $task) {

                $path = $this->parseUrl($task->url);
                $ownerType = $this->getOwnerType($path);

                if ($ownerType) {
                    $task->makeShadow();
                    $task->ownerType = $ownerType;
                    $this->factoryTasks->tasks->save($task);
                    $countUpdatedTasks++;
                    echo "\n" . $countUpdatedTasks;
                }

            }
        }


        if ($countUpdatedTasks) {
            echo "\naction=Tasks/Tasks/Check:UpdateTasks";
            echo $date;
            echo "\nВремя выполнения: " . round((time() - $tm) / 60, 2);
            echo "\nЗаданий проверено: " . $countTasks;
            echo "\nОбновлено заданий: " . $countUpdatedTasks;
            echo "\n";
        }

    }

    protected function parseUrl($url)
    {

        $path = '';
        $url = trim($url);
        $data = parse_url($url);

        if (!empty($data['query'])) {

            parse_str($data['query'], $query);

            //для ссылок вида: https://vk.com/team?w=wall-22822305_1229241
            // https://vk.com/vk?z=photo-22822305_457313982
            if (!empty($query['z'])) {
                list($path) = explode('/', $query['z']);
            } else if (!empty($query['w'])) {
                $path = $query['w'];
            }
        }

        if (empty($path)) $path = trim($data['path'], '/');

        return $path;
    }


    protected function getOwnerType($path)
    {
        if (preg_match('@^(photo|wall|post|video|topic)(\d+)_(\d+)@', $path, $matches)) {
            return 1;
        } else if (preg_match('@^(photo|wall|post|video|topic)-(\d+)_(\d+)@', $path, $matches)) {
            return 2;
        }
        return false;
    }
}
