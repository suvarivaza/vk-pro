<?php

namespace Service\Tasks;

/**
 * @property Model_Factory $factory
 */
class Model_Specials_Users extends \Lib_ORM
{
    public const TABLE = 'special_users';

    public const INDEX = 'PRIMARY';
    public const INDEX_TASKID = 'specialId';
    public const INDEX_USERID = 'userId';

    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $specialUser = new Model_Specials_Users_User($this);

        return $specialUser;
    }

    /**
     * @param $specialUserId
     * @param bool $for_save
     *
     * @return null| Model_Specials_Users_User
     */
    public function getById($specialUserId, $for_save = false)
    {
        $specialUser = new  Model_Specials_Users_User($this);

        if (!parent::_getOneByIndex($specialUserId, $specialUser, new \Database_Main(), self::TABLE, self::INDEX,
            $for_save)) {
            return null;
        }

        return $specialUser;
    }

    public function getBySpecialId($specialId, $for_save = false)
    {
        $specialUser = new  Model_Specials_Users_User($this);

        return parent::_getCollectionByIndex($specialId, $specialUser, new \Database_Main(), self::TABLE,
            self::INDEX_TASKID, $for_save);
    }

    public function getByUserId($userId, $for_save = false)
    {
        $specialUser = new  Model_Specials_Users_User($this);

        return parent::_getCollectionByIndex($userId, $specialUser, new \Database_Main(), self::TABLE,
            self::INDEX_USERID, $for_save);
    }

    public function getCountTotal($specialId)
    {
        $sql = 'SELECT COUNT(`specialUserId`) FROM `' . self::TABLE . '` WHERE `specialId` = ' . $specialId;
        $db = \Lib_DB_Factory::GetInstance(new \Database_Main());
        $res = $db->query($sql);
        list($total) = $res->fetch_row();

        return $total;
    }

    public function getCountOnline($specialId)
    {
        $sql = 'SELECT DISTINCT(`u`.`userId`) as `userId` FROM `' . self::TABLE . '` as `s`';
        $sql .= ' LEFT JOIN `' . \Service\Users\Model_Users::TABLE . '` as `u`';
        $sql .= ' ON `s`.`userId` = `u`.`userId`';
        $sql .= ' WHERE `u`.`lastLogin` > FROM_UNIXTIME(' . strtotime('-5 MINUTE') . ')';
        $sql .= ' AND `u`.`userId` IS NOT NULL';
        $sql .= ' AND `specialId` = ' . $specialId;

        $db = \Lib_DB_Factory::GetInstance(new \Database_Main());
        $res = $db->query($sql);
        $list = [];

        while ($row = $res->fetch_assoc()) {
            $list[$row['userId']] = $row['userId'];
        }

        $sql = 'SELECT DISTINCT(`b`.`userId`) as `userId` FROM `' . self::TABLE . '` as `s` LEFT JOIN `' . \Service\Bot\Model_Bots::TABLE . '` as `b` ON `s`.`userId` = `b`.`userId` WHERE `b`.`isActive` = 1 AND `s`.`specialId` = ' . $specialId;
        $res = $db->query($sql);

        while ($row = $res->fetch_assoc()) {
            $list[$row['userId']] = $row['userId'];
        }

        return count($list);
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
     * @param Model_Specials_Users_User $task
     *
     * @return bool|int|null
     */
    public function save(Model_Specials_Users_User $task)
    {
        if ($task->specialUserId) {
            $result = parent::_saveDifferencesByIndex($task->specialUserId, $task, new \Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($task, new \Database_Main(), self::TABLE, self::INDEX);
            $task->specialUserId = $result;
        }

        return $result;
    }

    public function delete(Model_Specials_Users_User $specialUser)
    {
        return parent::_deleteByIndex($specialUser->specialUserId, new \Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new  Model_Specials_Users_User($this), new \Database_Main(), self::TABLE);

        return $query;
    }
}
