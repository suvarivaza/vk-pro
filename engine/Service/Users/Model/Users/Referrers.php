<?php

namespace Service\Users;

/**
 * @property Model_Factory $factory
 */
class Model_Users_Referrers extends \Lib_ORM
{
    public const TABLE = 'users_referrers';

    public const INDEX = 'PRIMARY';
    public const INDEX_USERID = 'i_userId';

    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $referrer = new Model_Users_Referrers_Referrer($this);

        return $referrer;
    }

    /**
     * @param $referrerId
     * @param bool $for_save
     *
     * @return Model_Users_Referrers_Referrer|null
     */
    public function getById($referrerId, $for_save = false)
    {
        $referrer = new Model_Users_Referrers_Referrer($this);

        if (parent::_getOneByIndex($referrerId, $referrer, new \Database_Main(), self::TABLE, self::INDEX,
            $for_save ? true : false)) {
            return $referrer;
        }

        return null;
    }

    /**
     * @param $userId
     * @param bool $for_save
     *
     * @return Model_Users_Referrers_Referrer[]|null
     */
    public function getByUserId($userId, $for_save = false)
    {
        $referrer = new Model_Users_Referrers_Referrer($this);

        return parent::_getCollectionByIndex($userId, $referrer, new \Database_Main(), self::TABLE, self::INDEX_USERID,
            $for_save ? true : false);
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
     * @param Model_Users_Referrers_Referrer $referrer
     *
     * @return bool|int|null
     */
    public function save(Model_Users_Referrers_Referrer $referrer)
    {
        if ($referrer->referrerId) {
            $result = parent::_saveDifferencesByIndex($referrer->referrerId, $referrer, new \Database_Main(),
                self::TABLE, self::INDEX);
        } else {
            $result = parent::_insert($referrer, new \Database_Main(), self::TABLE, self::INDEX);
            $referrer->referrerId = $result;
        }

        return $result;
    }

    public function delete(Model_Users_Referrers_Referrer $referrer)
    {
        return parent::_deleteByIndex($referrer->referrerId, new \Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new Model_Users_Referrers_Referrer($this), new \Database_Main(), self::TABLE);

        return $query;
    }
}
