<?php

namespace Service\Users;

/**
 * @property Model_Factory $factory
 */
class Model_Users_Online extends \Lib_ORM
{
    public const TABLE = 'users_online';

    public const INDEX = 'PRIMARY';
    public const INDEX_DATE = 'i_date';

    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $userOnline = new Model_Users_Online_Online($this);

        return $userOnline;
    }

    /**
     * @param $userOnlineId
     * @param bool $for_save
     *
     * @return Model_Users_Online_Online|null
     */
    public function getById($userOnlineId, $for_save = false)
    {
        $userOnline = new Model_Users_Online_Online($this);

        if (parent::_getOneByIndex($userOnlineId, $userOnline, new \Database_Main(), self::TABLE, self::INDEX,
            $for_save ? true : false)) {
            return $userOnline;
        }

        return null;
    }

    public function getByKey($key, $for_save = false)
    {
        $userOnline = new Model_Users_Online_Online($this);

        if (parent::_getOneByIndex($key, $userOnline, new \Database_Main(), self::TABLE, self::INDEX_DATE,
            $for_save ? true : false)) {
            return $userOnline;
        }

        return null;
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
     * @param Model_Users_Online_Online $userOnline
     *
     * @return bool|int|null
     */
    public function save(Model_Users_Online_Online $userOnline)
    {
        if ($userOnline->userOnlineId) {
            $result = parent::_saveDifferencesByIndex($userOnline->userOnlineId, $userOnline, new \Database_Main(),
                self::TABLE, self::INDEX);
        } else {
            $result = parent::_insert($userOnline, new \Database_Main(), self::TABLE, self::INDEX);
            $userOnline->userOnlineId = $result;
        }

        return $result;
    }

    public function delete(Model_Users_Online_Online $userOnline)
    {
        return parent::_deleteByIndex($userOnline->userOnlineId, new \Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new Model_Users_Online_Online($this), new \Database_Main(), self::TABLE);

        return $query;
    }
}
