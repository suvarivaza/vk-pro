<?php

namespace Service\Tasks;

/**
 * @property Model_Factory $factory
 * @property Model_Specials_Users $users
 */
class Model_Specials_Groups extends \Lib_ORM
{
    public const TABLE = 'special_groups';

    public const INDEX = 'PRIMARY';
    public const INDEX_USERID = 'i_userId';
    public const INDEX_OWNERID = 'i_ownerId';

    /** @var Model_Factory */
    protected $_factory;

    private $_users = null;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $group = new Model_Specials_Groups_Group($this);
        $group->dateCreate = time();
        $group->isFree = false;
        $group->wasFree = false;
        $group->isFreeCount = 0;
        $group->isActive = true;

        return $group;
    }

    /**
     * @param $groupId
     * @param bool $for_save
     *
     * @return null| Model_Specials_Groups_Group
     */
    public function getById($groupId, $for_save = false)
    {
        $group = new  Model_Specials_Groups_Group($this);

        if (!parent::_getOneByIndex($groupId, $group, new \Database_Main(), self::TABLE, self::INDEX, $for_save)) {
            return null;
        }

        return $group;
    }

    /**
     * @param specialId
     * @param bool $for_save
     *
     * @return null| Model_Specials_Groups_Group
     */
    public function getByOwnerId($ownerId, $for_save = false)
    {
        $group = new  Model_Specials_Groups_Group($this);

        if (!parent::_getOneByIndex($ownerId, $group, new \Database_Main(), self::TABLE, self::INDEX_OWNERID,
            $for_save)) {
            return null;
        }

        return $group;
    }

    /**
     * @param $userId
     * @param bool $for_save
     *
     * @return Model_Specials_Groups_Group[]
     */
    public function getByUserId($userId, $for_save = false)
    {
        $special = new  Model_Specials_Groups_Group($this);

        return parent::_getCollectionByIndex($userId, $special, new \Database_Main(), self::TABLE, self::INDEX_USERID,
            $for_save);
    }

    /**
     * @param Model_Specials_Groups_Group $group
     *
     * @return bool|int|null
     */
    public function save(Model_Specials_Groups_Group $group)
    {
        if ($group->groupId) {
            $result = parent::_saveDifferencesByIndex($group->groupId, $group, new \Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($group, new \Database_Main(), self::TABLE, self::INDEX);
            $group->groupId = $result;
        }

        return $result;
    }

    public function delete(Model_Specials_Groups_Group $group)
    {
        return parent::_deleteByIndex($group->groupId, new \Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new  Model_Specials_Groups_Group($this), new \Database_Main(), self::TABLE);

        return $query;
    }

    public function __get($name)
    {
        switch ($name) {
            case 'factory':
                return $this->_factory;
            case 'users':
                if ($this->_users === null) {
                    $this->_users = new Model_Specials_Users($this->factory);
                }

                return $this->_users;
        }

        throw new \Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }

    public function getCounts($free = false)
    {
        $sql = 'SELECT COUNT(DISTINCT(`userId`)) FROM `' . self::TABLE . '`';
        $sql .= ' WHERE `dateValid` > NOW() ';

        if ($free === true) {
            $sql .= ' AND `isFree` = 1 AND `isFreeCount` > 0';
        } else {
            $sql .= ' AND NOT (`isFree` = 1 AND `isFreeCount` = 0)';
        }

        $res = $this->factory->db->query($sql);

        $row = $res->fetch_row();

        return $row[0];
    }
}
