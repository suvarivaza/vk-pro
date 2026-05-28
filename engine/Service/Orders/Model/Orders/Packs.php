<?php

namespace Service\Orders;

/**
 * @property Model_Factory $factory
 */
class Model_Orders_Packs extends \Lib_ORM
{
    public const TABLE = 'orders_packs';

    public const INDEX = 'PRIMARY';

    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    /**
     * @return Model_Orders_Packs_Pack
     */
    public function getNew()
    {
        $pack = new Model_Orders_Packs_Pack($this);
        $pack->profit = 0.0;

        return $pack;
    }

    /**
     * @param $packId
     * @param bool $for_save
     *
     * @return null| Model_Orders_Packs_Pack
     */
    public function getById($packId, $for_save = false)
    {
        $pack = new  Model_Orders_Packs_Pack($this);

        if (!parent::_getOneByIndex($packId, $pack, new \Database_Main(), self::TABLE, self::INDEX, $for_save)) {
            return null;
        }

        return $pack;
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
     * @param Model_Orders_Packs_Pack $pack
     *
     * @return bool|int|null
     */
    public function save(Model_Orders_Packs_Pack $pack)
    {
        if ($pack->packId) {
            $result = parent::_saveDifferencesByIndex($pack->packId, $pack, new \Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($pack, new \Database_Main(), self::TABLE, self::INDEX);
            $pack->packId = $result;
        }

        return $result;
    }

    public function delete(Model_Orders_Packs_Pack $pack)
    {
        return parent::_deleteByIndex($pack->packId, new \Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new  Model_Orders_Packs_Pack($this), new \Database_Main(), self::TABLE);

        return $query;
    }
}
