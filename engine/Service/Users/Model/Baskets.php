<?php

namespace Service\Users;

/**
 * @property Model_Factory $factory
 */
class Model_Baskets extends \Lib_ORM
{
    public const TABLE = 'basket';

    public const INDEX = 'PRIMARY';
    public const INDEX_USERID = 'userId';
    public const INDEX_USERCOOKIE = 'userCookie';

    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $basket = new Model_Baskets_Basket($this);

        return $basket;
    }

    /**
     * @param $basketId
     * @param bool $for_save
     *
     * @return Model_Baskets_Basket|null
     */
    public function getById($basketId, $for_save = false)
    {
        $basket = new Model_Baskets_Basket($this);

        if (parent::_getOneByIndex($basketId, $basket, new \Database_Main(), self::TABLE, self::INDEX,
            $for_save ? true : false)) {
            return $basket;
        }

        return null;
    }

    /**
     * @param $userId
     * @param bool $for_save
     *
     * @return Model_Baskets_Basket|null
     */
    public function getByUserId($userId, $for_save = false)
    {
        $basket = new Model_Baskets_Basket($this);

        if (parent::_getOneByIndex($userId, $basket, new \Database_Main(), self::TABLE, self::INDEX_USERID,
            $for_save ? true : false)) {
            return $basket;
        }

        return null;
    }

    /**
     * @param $userCookie
     * @param bool $for_save
     *
     * @return Model_Baskets_Basket|null
     */
    public function getByUserCookie($userCookie, $for_save = false)
    {
        $basket = new Model_Baskets_Basket($this);

        if (parent::_getOneByIndex($userCookie, $basket, new \Database_Main(), self::TABLE, self::INDEX_USERCOOKIE,
            $for_save ? true : false)) {
            return $basket;
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
     * @param Model_Users_User $user
     *
     * @return bool|int|null
     */
    public function save(Model_Baskets_Basket $basket)
    {
        if ($basket->basketId) {
            $result = parent::_saveDifferencesByIndex($basket->basketId, $basket, new \Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($basket, new \Database_Main(), self::TABLE, self::INDEX);
            $basket->basketId = $result;
        }

        return $result;
    }

    public function delete(Model_Baskets_Basket $basket)
    {
        return parent::_deleteByIndex($basket->basketId, new \Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new Model_Users_User($this), new \Database_Main(), self::TABLE);

        return $query;
    }
}
