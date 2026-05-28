<?php

namespace Service\Tasks;

/**
 * @property Model_Factory $factory
 */
class Model_Tasks extends \Lib_ORM
{
    public const TABLE = 'tasks';

    public const INDEX = 'PRIMARY';
    public const INDEX_ALIAS = 'i_type_count_dateCreate';
    public const INDEX_SPECIAL = 'i_specialId';

    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $task = new Model_Tasks_Task($this);
        $task->isDel = false;
        $task->active = true;
        $task->title = '';
        $task->commentId = '';
        $task->targeting = false;
        $task->pollId = 0;
        $task->answerId = 0;
        $task->answerTitle = '';
        $task->answerIds = '';
        $task->countMinute = 0;
        $task->count10Min = 0;
        $task->countHour = 0;
        $task->countDay = 0;
        $task->isSpecial = false;
        $task->specialId = 0;
        $task->price = 0.0;
        $task->sum = 0.0;
        $task->dateCreate = time();
        $task->countReadyBot = 0;

        $task->sex = 0;
        $task->ageFrom = 0;
        $task->ageTo = 0;
        $task->cityId = 0;
        $task->countryId = 0;
        $task->relation = 0;
        $task->avatarCount = 0;
        $task->filled = 0;
        $task->pageAge = 0;
        $task->followersCount = 0;
        $task->interestingPage = 0;
        $task->frequencyPost = 0;
        $task->isSpecialInvite = false;

        $task->isTemplate = false;
        $task->templateId = 0;

        $task->isAnonymous = false;
        $task->age_limits = 0;

        $task->setPhoto([]);

        return $task;
    }

    /**
     * @param $taskId
     * @param bool $for_save
     *
     * @return null| Model_Tasks_Task
     */
    public function getById($taskId, $for_save = false)
    {
        $task = new  Model_Tasks_Task($this);

        if (!parent::_getOneByIndex($taskId, $task, new \Database_Main(), self::TABLE, self::INDEX, $for_save)) {
            return null;
        }

        return $task;
    }

    public function getBySpecialId($specialId, $for_save = false)
    {
        $obj = new Model_Tasks_Task($this);

        return parent::_getCollectionByIndex($specialId, $obj, new \Database_Main(), self::TABLE, self::INDEX_SPECIAL,
            $for_save);
    }

    public function __get($name)
    {
        switch ($name) {
            case 'factory':
                return $this->_factory;
        }

        throw new \Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }

    /**
     * @param Model_Tasks_Task $task
     *
     * @return bool|int|null
     */
    public function save(Model_Tasks_Task $task)
    {
        if ($task->taskId) {
            $shadow = $task->getShadow();

            if ($task->countRemain == 0) {
                if ($shadow->countRemain > 0) {
                    $factoryMessages = new \Service\Messages\Model_Factory();
                    $mConfig = \Service\Messages\Model_Config::GetConfig();

                    $message = $factoryMessages->users->getNew();
                    $message->userId = $task->userId;
                    $message->isDone = false;
                    $message->type = \Service\Messages\Model_Config::TYPE_SYSTEM;
                    $text = $mConfig['tasks']['types']['done']['text'];
                    $text = str_replace('%title%', $task->title, $text);
                    $message->text = $text;

                    $message->icon = 'tasks';
                    $factoryMessages->users->save($message);
                }
            }
            $result = parent::_saveDifferencesByIndex($task->taskId, $task, new \Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($task, new \Database_Main(), self::TABLE, self::INDEX);
            $task->taskId = $result;
        }

        return $result;
    }

    public function delete(Model_Tasks_Task $task)
    {
        return parent::_deleteByIndex($task->taskId, new \Database_Main(), self::TABLE, self::INDEX);
    }

    public function getStatDefault()
    {
        $sql = 'SELECT `type`, COUNT(`taskId`) as `count` FROM `tasks` GROUP BY `type`';
        $res = $this->factory->db->query($sql);
        $all = 0;
        $type = [];

        while ($row = $res->fetch_assoc()) {
            $all += $row['count'];
            $type['all'][$row['type']] = $row['count'];
        }

        $sql = 'SELECT `type`, COUNT(`taskId`) as `count` FROM `tasks` WHERE `isDel` = 0 AND `countRemain` > 0 AND `active` = 1 GROUP BY `type`';
        $res = $this->factory->db->query($sql);
        $active = 0;

        while ($row = $res->fetch_assoc()) {
            $active += $row['count'];
            $type['active'][$row['type']] = $row['count'];
        }

        $sql = 'select `type`, COUNT(`taskId`) as `count` FROM `tasks` WHERE `countRemain` = 0 GROUP BY `type`';
        $res = $this->factory->db->query($sql);
        $done = 0;

        while ($row = $res->fetch_assoc()) {
            $done += $row['count'];
            $type['done'][$row['type']] = $row['count'];
        }

        $sql = 'SELECT `type`, COUNT(`taskId`) as `count` FROM `tasks` WHERE `isDel` = 1 AND `countRemain` > 0 GROUP BY `type`';
        $res = $this->factory->db->query($sql);
        $isDel = 0;

        while ($row = $res->fetch_assoc()) {
            $isDel += $row['count'];
            $type['isDel'][$row['type']] = $row['count'];
        }

        return [
            'isDel' => $isDel,
            'active' => $active,
            'done' => $done,
            'all' => $all,
            'type' => $type,
        ];
    }

    public function getStatByDay($from = null, $to = null, $del = false, $active = false, $done = false)
    {
        if ($from === null) {
            $from = strtotime('-30 DAY');
        }

        if ($to === null) {
            $to = time();
        }

        $to = strtotime('+1 DAY', $to);

        $sql = "SELECT DATE_FORMAT(`dateCreate`, '%Y-%m-%d') as `day`, COUNT(`taskId`) as `count` FROM `" . self::TABLE . '`';
        $sql .= " WHERE `dateCreate` > '" . date('Y-m-d', $from) . "'";
        $sql .= " AND `dateCreate` < '" . date('Y-m-d', $to) . "'";

        if ($del === true) {
            $sql .= ' AND `isDel` = 1 AND `countRemain` > 0';
        }

        if ($active === true) {
            $sql .= ' AND `isDel` = 0 AND `countRemain` > 0 AND `active` = 1';
        }

        if ($done === true) {
            $sql .= ' AND `countRemain` = 0';
        }
        $sql .= ' GROUP BY `day` ORDER BY `day`';
        $res = $this->factory->db->query($sql);

        $list = [];

        while ($row = $res->fetch_assoc()) {
            $list[$row['day']] = $row['count'];
        }

        return $list;
    }

    public function getCountTotal($special = false)
    {
        $sql = 'SELECT COUNT(`taskId`) FROM `' . self::TABLE . '` WHERE `isSpecial` = ' . intval($special);
        $res = $this->factory->db->query($sql);
        $row = $res->fetch_row();

        return $row[0];
    }

    public function getListSpecials(\Service\Users\Model_Users_User $user, $type, $limit, $offset, $lastTime = null)
    {
        $where = [
            '(`t`.`userId` != ' . $user->userId . ')',
            '(`t`.`active` = 1)',
            '(`t`.`countRemain` > 0)',
        ];

        if ($type != 'all') {
            $where[] = "(`t`.`type` = '" . $type . "')";
            $where[] = '(`t`.`isSpecial` = 1)';
        }

        if ($user->balance < 0) {
            $where[] = "`t`.`type` IN ('views', 'video', 'polls')";
        }

        $where[] = '(`t`.`sex` = 0 OR `t`.`sex` = ' . $user->sex . ')';
        $where[] = '(`t`.`ageFrom` = 0 OR `t`.`ageFrom` < ' . $user->age . ')';
        $where[] = '(`t`.`ageTo` = 0 OR `t`.`ageTo` > ' . $user->age . ')';
        $where[] = '(`t`.`cityId` = 0 OR `t`.`cityId` = ' . $user->cityId . ')';
        $where[] = '(`t`.`countryId` = 0 OR `t`.`countryId` = ' . $user->countryId . ')';
        $where[] = '(`t`.`relation` = 0 OR `t`.`relation` = ' . $user->relation . ')';
        $where[] = '(`t`.`avatarCount` = 0 OR `t`.`avatarCount` < ' . $user->avatarCount . ')';
        $where[] = '(`t`.`filled` = 0 OR `t`.`filled` < ' . $user->partCount . ')';
        $where[] = '(`t`.`pageAge` = 0 OR `t`.`pageAge` <= ' . $user->pageAge . ')';
        $where[] = '(`t`.`followersCount` = 0 OR `t`.`followersCount` < ' . $user->followersCount . ')';
        $where[] = '(`t`.`interestingPage` = 0 OR `t`.`interestingPage` > ' . $user->pagesCount . ')';
        $where[] = '(`t`.`frequencyPost` = 0 OR `t`.`frequencyPost` > ' . $user->frequency . ')';
        $where[] = '(`t`.`minKarma` = 0 OR `t`.`minKarma` < ' . $user->karma . ')';

        if ($lastTime !== null) {
            $where[] = "(`t`.`dateLast` IS NULL OR `t`.`dateLast` < '" . date('Y-m-d H:i:s', $lastTime) . "')";
        }

        $sql = 'SELECT `t`.* FROM `' . self::TABLE . '` as `t` LEFT OUTER JOIN `' . Model_Users::TABLE . '` as `tu` ON `t`.`taskId` = `tu`.`taskId` AND `tu`.`userId` = ' . $user->userId;
        $sql .= ' LEFT JOIN `' . Model_Specials_Users::TABLE . '` as `su` ON `su`.`userId` = ' . $user->userId . ' AND `t`.`specialId` = `su`.`specialId`';
        $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= ' AND (`su`.`userId` IS NOT NULL OR `t`.`isSpecialInvite` = 1)';
        $sql .= ' AND `t`.`isDel` = 0 AND (`tu`.`taskId` IS NULL OR (`tu`.`isDel` = 0 AND `tu`.`isDone` = 0 AND `tu`.`isActive` = 0)) ORDER BY `prior` DESC';
        if ($limit !== null and $offset !== null) $sql .= " LIMIT $limit OFFSET $offset";

        $db = \Lib_DB_Factory::GetInstance(new \Database_Main());
        $res = $db->query($sql);

        $list = [];

        while ($row = $res->fetch_array()) {
            $task = new Model_Tasks_Task($this);
            $task->taskId = intval($row['taskId']);
            $task->type = $row['type'];
            $task->userId = intval($row['userId']);
            $task->dateCreate = strtotime($row['dateCreate']);
            $task->url = $row['url'];
            $task->vkType = $row['vkType'];
            $task->ownerId = $row['ownerId'];
            $task->ownerType = $row['ownerType'];
            $task->itemId = $row['itemId'];
            $task->commentId = $row['commentId'];
            $task->minKarma = intval($row['minKarma']);
            $task->followersOnly = boolval($row['followersOnly']);
            $task->newFollowers = boolval($row['newFollowers']);
            $task->prior = boolval($row['prior']);
            $task->count = intval($row['count']);
            $task->countReady = intval($row['countReady']);
            $task->countRemain = intval($row['countRemain']);
            $task->countReadyBot = intval($row['countReadyBot']);
            $task->active = boolval($row['active']);
            $task->isDel = boolval($row['isDel']);
            $task->title = $row['title'];
            $task->commentType = $row['commentType'];
            $task->answerTitle = $row['answerTitle'];

            $task->isSpecial = boolval($row['isSpecial']);
            $task->isSpecialInvite = boolval($row['isSpecialInvite']);
            $task->specialId = intval($row['specialId']);

            $task->pollId = intval($row['pollId']);
            $task->answerIds = $row['answerIds'];
            $task->answerId = intval($row['answerId']);
            $task->answerTitle = $row['answerTitle'];

            $task->comments = $row['comments'];
            $task->photo = $row['photo'];
            $list[] = $task;
        }

        return $list;
    }

    public function getItemBonus(\Service\Users\Model_Users_User $user)
    {
        if ($user->bad > 0) {
            $list = $this->getItemsList($user, 'views', 20, 0);
            shuffle($list);

            if (count($list) > 0) {
                return array_shift($list);
            }

            $list = $this->getItemsList($user, 'video', 20, 0);
            shuffle($list);

            if (count($list) > 0) {
                return array_shift($list);
            }

            return [];
        }

        $list = $this->getItemsList($user, 'join', 20, 0);
        shuffle($list);

        if (count($list) > 0) {
            return array_shift($list);
        }

        $list = $this->getItemsList($user, 'friends', 20, 0);
        shuffle($list);

        if (count($list) > 0) {
            return array_shift($list);
        }

        $list = $this->getItemsList($user, 'reposts', 20, 0);
        shuffle($list);

        if (count($list) > 0) {
            return array_shift($list);
        }

        $list = $this->getItemsList($user, 'views', 20, 0);
        shuffle($list);

        if (count($list) > 0) {
            return array_shift($list);
        }

        $list = $this->getItemsList($user, 'video', 20, 0);
        shuffle($list);

        if (count($list) > 0) {
            return array_shift($list);
        }

        return null;
    }

    /**
     * @param $userId
     * @param $limit
     * @param $offset
     *
     * @return Model_Tasks_Task[]
     */
    public function getItemsList(
        \Service\Users\Model_Users_User $user,
                                        $type,
                                        $limit,
                                        $offset,
                                        $taskId = 0,
                                        $lastTime = null,
                                        $logdb = false,
                                        $maxReady = 0
    )
    {


        $where = [];

        if ($taskId > 0) {

            $where = ['(`t`.`taskId` = ' . $taskId . ')'];

            if ($type != 'all') {
                $where[] = "(`t`.`type` = '" . $type . "')";
                $where[] = '(`t`.`isSpecial` = 0)';
            }

        } else {

            $where[] = '(`t`.`userId` != ' . $user->userId . ')'; //задание не пренадлежит пользователю
            $where[] = '(`t`.`active` = 1)';
            $where[] = '(`t`.`countRemain` > 0)';

            if ($type != 'all') {
                $where[] = "(`t`.`type` = '" . $type . "')";
                $where[] = '(`t`.`isSpecial` = 0)';
                $where[] = '(`t`.`isDel` = 0)';
            }

            //если баланс пользователя меньше 0 то ему доступны только эти типы заданий
            if ($user->balance < 0) {
                $where[] = "`t`.`type` IN ('views', 'video', 'polls')";
            }

            $where[] = '(`t`.`sex` = 0 OR `t`.`sex` = ' . $user->sex . ')';
            $where[] = '(`t`.`ageFrom` = 0 OR `t`.`ageFrom` < ' . $user->age . ')';
            $where[] = '(`t`.`ageTo` = 0 OR `t`.`ageTo` > ' . $user->age . ')';
            $where[] = '(`t`.`cityId` = 0 OR `t`.`cityId` = ' . $user->cityId . ')';
            $where[] = '(`t`.`countryId` = 0 OR `t`.`countryId` = ' . $user->countryId . ')';
            $where[] = '(`t`.`relation` = 0 OR `t`.`relation` = ' . $user->relation . ')';
            $where[] = '(`t`.`avatarCount` = 0 OR `t`.`avatarCount` < ' . $user->avatarCount . ')';
            $where[] = '(`t`.`filled` = 0 OR `t`.`filled` < ' . $user->partCount . ')';
            $where[] = '(`t`.`pageAge` = 0 OR `t`.`pageAge` <= ' . $user->pageAge . ')';
            $where[] = '(`t`.`followersCount` = 0 OR `t`.`followersCount` < ' . $user->followersCount . ')';
            $where[] = '(`t`.`interestingPage` = 0 OR `t`.`interestingPage` > ' . $user->pagesCount . ')';
            $where[] = '(`t`.`frequencyPost` = 0 OR `t`.`frequencyPost` > ' . $user->frequency . ')';
            $where[] = '(`t`.`minKarma` = 0 OR `t`.`minKarma` < ' . $user->karma . ')';


            //выбираем задания которые еще вообще не выполнялись (dateLast IS NULL)
            // или у которых время выполнения последнего задания меньше чем переданое значение $lastTime
            // для чего это? текущее время ведь всегда больше чем dateLast???
            if ($lastTime !== null) {
                $where[] = "(`t`.`dateLast` IS NULL OR `t`.`dateLast` < '" . date('Y-m-d H:i:s', $lastTime) . "')";
            }

            if ($maxReady > 0) {
                $where[] = '(`t`.`countReadyBot` / `count` < ' . $maxReady . ')';
            }
        }

        if ($user->age_limits > 0) {
            $where[] = '(`t`.`age_limits` <= ' . $user->age_limits . ')';
        }

        //выборка из двух таблиц tasks и tasks_users
        $sql = 'SELECT `t`.* FROM `' . self::TABLE . '` as `t` LEFT OUTER JOIN `' . Model_Users::TABLE . '` as `tu` ON `t`.`taskId` = `tu`.`taskId` AND `tu`.`userId` = ' . $user->userId;
        $sql .= ' WHERE ' . implode(' AND ', $where);

        // важно для формирования листа заданий для пользователя
        // проверим что данный пользователь не выполнял уже данные задания! проерим в заданиях пользователя таблица tasks_users
        if ($taskId == 0) {
            $sql .= ' AND (`tu`.`taskId` IS NULL OR (`tu`.`isDel` = 0 AND `tu`.`isDone` = 0 AND `tu`.`isActive` = 0)) ORDER BY `prior` DESC';
            if ($limit !== null and $offset !== null) $sql .= " LIMIT $limit OFFSET $offset";
        }

        if ($logdb) {
            //error_log($sql);
        }

        $db = \Lib_DB_Factory::GetInstance(new \Database_Main());
        $res = $db->query($sql);

        $list = [];

        while ($row = $res->fetch_array()) {
            $task = new Model_Tasks_Task($this);
            $task->taskId = intval($row['taskId']);
            $task->type = $row['type'];
            $task->userId = intval($row['userId']);
            $task->dateCreate = strtotime($row['dateCreate']);
            $task->url = $row['url'];
            $task->vkType = $row['vkType'];
            $task->ownerId = $row['ownerId'];
            $task->ownerType = $row['ownerType'];
            $task->itemId = $row['itemId'];
            $task->commentId = $row['commentId'];
            $task->minKarma = intval($row['minKarma']);
            $task->followersOnly = boolval($row['followersOnly']);
            $task->newFollowers = boolval($row['newFollowers']);
            $task->prior = boolval($row['prior']);
            $task->count = intval($row['count']);
            $task->countReady = intval($row['countReady']);
            $task->countRemain = intval($row['countRemain']);
            $task->countReadyBot = intval($row['countReadyBot']);
            $task->active = boolval($row['active']);
            $task->isDel = boolval($row['isDel']);
            $task->title = $row['title'];
            $task->commentType = $row['commentType'];

            $task->isSpecial = boolval($row['isSpecial']);
            $task->isSpecialInvite = boolval($row['isSpecialInvite']);
            $task->specialId = intval($row['specialId']);

            $task->pollId = intval($row['pollId']);
            $task->answerIds = $row['answerIds'];
            $task->answerId = intval($row['answerId']);
            $task->answerTitle = $row['answerTitle'];

            $task->comments = $row['comments'];
            $task->photo = $row['photo'];
            $list[] = $task;
        }

        return $list;
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new  Model_Tasks_Task($this), new \Database_Main(), self::TABLE);

        return $query;
    }

    public function findTask($fields)
    {

        if ($fields['ownerId']) $fields['ownerId'] = '-' . abs(intval($fields['ownerId']));

        $query = $this->query();
        $query->sqlCalcFoundRows(true)->limit(1)->sort('taskId', 'DESC');
        if ($fields['userId']) $query->filter->fieldValue('userId', '=', $fields['userId']);
        if ($fields['type']) $query->filter->fieldValue('type', '=', $fields['type']);
        if ($fields['ownerId']) $query->filter->fieldValue('ownerId', '=', $fields['ownerId']);
        if ($fields['itemId']) $query->filter->fieldValue('itemId', '=', $fields['itemId']);
        $it = $query->iterator();
        //$total = $it->getTotal();
        $task = $it->current();

        return $task;
    }

    public function clearLimits($period)
    {
        $sql = 'UPDATE `' . self::TABLE . '` SET ';
        $sql .= '`' . $period . '` = 0';
        $sql .= ' WHERE `' . $period . '` > 0';
        $db = \Lib_DB_Factory::GetInstance(new \Database_Main());
        $res = $db->query($sql);
        \Lib_DB_Factory::Flush();

        return true;
    }
}
