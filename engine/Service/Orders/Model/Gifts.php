<?php

namespace Service\Orders;

/**
 * @property Model_Factory $factory
 */
class Model_Gifts extends \Lib_ORM
{
    public const TABLE = 'gifts';

    public const INDEX = 'PRIMARY';
    public const INDEX_USERID = 'i_userId';
    public const INDEX_ADMINID = 'i_adminId';

    /** @var Model_Factory */
    protected $_factory;

    private $_packs = null;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $gift = new Model_Gifts_Gift($this);
        $gift->dateCreate = time();

        return $gift;
    }

    /**
     * @param giftId
     * @param bool $for_save
     *
     * @return null| Model_Gifts_Gift
     */
    public function getById($giftId, $for_save = false)
    {
        $gift = new  Model_Gifts_Gift($this);

        if (!parent::_getOneByIndex($giftId, $gift, new \Database_Main(), self::TABLE, self::INDEX, $for_save)) {
            return null;
        }

        return $gift;
    }

    /**
     * @param $userId
     * @param bool $for_save
     *
     * @return Model_Gifts_Gift[]
     */
    public function getByUserId($userId, $for_save = false, $limit = 100000)
    {
        $gift = new  Model_Gifts_Gift($this);

        return parent::_getCollectionByIndex($userId, $gift, new \Database_Main(), self::TABLE, self::INDEX_USERID,
            $for_save, $limit);
    }

    /**
     * @param Model_Gifts_Gift $gift
     *
     * @return bool|int|null
     */
    public function save(Model_Gifts_Gift $gift)
    {
        if ($gift->giftId) {
            $result = parent::_saveDifferencesByIndex($gift->giftId, $gift, new \Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($gift, new \Database_Main(), self::TABLE, self::INDEX);
            $gift->giftId = $result;
        }

        return $result;
    }

    public function delete(Model_Gifts_Gift $gift)
    {
        return parent::_deleteByIndex($gift->giftId, new \Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new  Model_Gifts_Gift($this), new \Database_Main(), self::TABLE);

        return $query;
    }

    public function __get($name)
    {
        switch ($name) {
            case 'factory':
                return $this->_factory;
        }

        throw new \Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }
}
