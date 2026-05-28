<?php

namespace Service\Auto;

use Database_Main;
use Lib_Exception_UnknownProperty_Backtraced;
use Lib_ORM;
use Lib_ORM_Query;

/**
 * @property Model_Factory $factory
 */
class Model_Autos_Groups extends Lib_ORM
{
    public const TABLE = 'auto_groups';

    public const INDEX = 'PRIMARY';
    public const INDEX_USERID = 'i_userId';
    public const INDEX_AUTOID = 'i_autoId';
    public const INDEX_USERID_OWNERID = 'i_userId_ownerId';

    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $group = new Model_Autos_Groups_Group($this);
        $group->photo = '';
        $group->isFree = false;
        $group->wasFree = false;
        $group->isFreeCount = 0;

        return $group;
    }

    /**
     * @param $groupId
     * @param bool $for_save
     *
     * @return null| Model_Autos_Groups_Group
     */
    public function getById($groupId, $for_save = false)
    {
        $group = new  Model_Autos_Groups_Group($this);

        if (!parent::_getOneByIndex($groupId, $group, new Database_Main(), self::TABLE, self::INDEX, $for_save)) {
            return null;
        }

        return $group;
    }

    /**
     * @param $userId
     * @param $ownerId
     * @param bool $for_save
     *
     * @return null| Model_Autos_Groups_Group
     */
    public function getByUserIdOwnerId($userId, $ownerId, $for_save = false)
    {
        $group = new  Model_Autos_Groups_Group($this);

        if (!parent::_getOneByIndex([$userId, $ownerId], $group, new Database_Main(), self::TABLE,
            self::INDEX_USERID_OWNERID, $for_save)) {
            return null;
        }

        return $group;
    }

    /**
     * @param $userId
     * @param bool $for_save
     *
     * @return Model_Autos_Groups_Group[]
     */
    public function getByUserId($userId, $for_save = false)
    {
        $group = new  Model_Autos_Groups_Group($this);

        return parent::_getCollectionByIndex($userId, $group, new Database_Main(), self::TABLE, self::INDEX_USERID,
            $for_save);
    }

    /**
     * @param $autoId
     * @param bool $for_save
     *
     * @return Model_Autos_Groups_Group[]
     */
    public function getByAutoId($autoId, $for_save = false)
    {
        $group = new  Model_Autos_Groups_Group($this);

        return parent::_getCollectionByIndex($autoId, $group, new Database_Main(), self::TABLE, self::INDEX_AUTOID,
            $for_save);
    }

    public function __get($name)
    {
        switch ($name) {
            case 'factory':
                return $this->_factory;
        }

        throw new Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }

    /**
     * @param Model_Autos_Groups_Group $group
     *
     * @return bool|int|null
     */
    public function save(Model_Autos_Groups_Group $group)
    {
        if ($group->autoGroupId) {
            $result = parent::_saveDifferencesByIndex($group->autoGroupId, $group, new Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($group, new Database_Main(), self::TABLE, self::INDEX);
            $group->autoGroupId = $result;
        }

        return $result;
    }

    public function delete(Model_Autos_Groups_Group $group)
    {
        return parent::_deleteByIndex($group->autoGroupId, new Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return Lib_ORM_Query
     */
    public function query()
    {
        $query = new Lib_ORM_Query(new  Model_Autos_Groups_Group($this), new Database_Main(), self::TABLE);

        return $query;
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
