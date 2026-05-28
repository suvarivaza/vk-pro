<?php

namespace Service\Tasks;

/**
 * @property Model_Factory $factory
 */
class Model_Users extends \Lib_ORM
{
    public const TABLE = 'tasks_users';

    public const INDEX = 'PRIMARY';
    public const INDEX_TASKID = 'i_taskId';
    public const INDEX_USERID = 'i_userId';
    public const INDEX_TASKID_USERID = 'u_taskId_userId';
    public const INDEX_ISDEL = 'i_isDel';

    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $taskUser = new Model_Users_User($this);
        $taskUser->views = 0;
        $taskUser->countViews = 0;
        $taskUser->uid = 0;
        $taskUser->votes = 0;
        $taskUser->isBot = false;

        return $taskUser;
    }

    /**
     * @param $taskUserId
     * @param bool $for_save
     *
     * @return null| Model_Users_User
     */
    public function getById($taskUserId, $for_save = false)
    {
        $taskUser = new  Model_Users_User($this);

        if (!parent::_getOneByIndex($taskUserId, $taskUser, new \Database_Main(), self::TABLE, self::INDEX,
            $for_save)) {
            return null;
        }

        return $taskUser;
    }

    /**
     * @param $taskId
     * @param $userId
     * @param bool $for_save
     *
     * @return Model_Users_User|null
     */
    public function getByTaskIdUserId($taskId, $userId, $for_save = false)
    {
        $taskUser = new  Model_Users_User($this);

        if (!parent::_getOneByIndex([$taskId, $userId], $taskUser, new \Database_Main(), self::TABLE,
            self::INDEX_TASKID_USERID, $for_save)) {
            return null;
        }

        return $taskUser;
    }

    /**
     * @param $taskId
     * @param false $for_save
     * @return \Lib_ORM_Object[]
     * @throws \Lib_Exception_Logic_Backtraced
     */
    public function getByTaskId($taskId, $for_save = false)
    {
        $taskUser = new  Model_Users_User($this);

        return parent::_getCollectionByIndex($taskId, $taskUser, new \Database_Main(), self::TABLE, self::INDEX_TASKID,
            $for_save);
    }

    public function getByUserId($userId, $for_save = false)
    {
        $taskUser = new  Model_Users_User($this);

        return parent::_getCollectionByIndex($userId, $taskUser, new \Database_Main(), self::TABLE, self::INDEX_USERID,
            $for_save);
    }

    public function getStatisticByUserId($userId = 0, $date)
    {
        $time = strtotime('midnight', $date);
        $from = date('Y-m-d', $time);
        $to = date('Y-m-d', strtotime('tomorrow', $time));

        $sql = 'SELECT `type`, count(`taskUserId`) as `count` FROM `' . self::TABLE . '` WHERE `userId` = ' . $userId . " AND `isDone` = 1 AND `isDoneDate` > '" . $from . "' AND `isDoneDate` < '" . $to . "' GROUP BY `type`";
        $res = $this->factory->db->query($sql);
        $rows = $res->fetchAll();


        $result = [
            'comments' => 0,
            'join' => 0,
            'friends' => 0,
            'likes' => 0,
            'polls' => 0,
            'reposts' => 0,
            'video' => 0,
            'views' => 0,
        ];

        foreach ($rows as $row) {
            $result[$row['type']] = $row['count'];
        }

        return $result;
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
     * @param Model_Users_User $task
     *
     * @return bool|int|null
     */
    public function save(Model_Users_User $task)
    {
        if ($task->taskUserId) {
            $result = parent::_saveDifferencesByIndex($task->taskUserId, $task, new \Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($task, new \Database_Main(), self::TABLE, self::INDEX);
            $task->taskUserId = $result;
        }

        return $result;
    }

    public function delete(Model_Users_User $taskUser)
    {
        return parent::_deleteByIndex($taskUser->taskUserId, new \Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new  Model_Users_User($this), new \Database_Main(), self::TABLE);

        return $query;
    }
}
