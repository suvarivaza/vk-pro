<?php

namespace System;

/**
 * @property Model_Factory $factory
 */
class Model_Proxies extends \Lib_ORM
{
    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNewItem()
    {
        $item = new Model_Proxies_Proxy($this);

        return $item;
    }

    /**
     * @param int $proxyId
     * @param bool $for_save
     *
     * @return Model_Proxies_Proxy|null
     */
    public function getByProxyId($proxyId, $for_save = false)
    {
        $obj = new Model_Proxies_Proxy($this);

        if (!parent::_getOneByIndex($proxyId, $obj, new \Database_General(), Model_Proxies_Proxy::TABLE, Model_Proxies_Proxy::PRIMARY, $for_save)) {
            return null;
        }

        return $obj;
    }

    /**
     * @param int $blocked
     * @param bool $for_save
     *
     * @return Model_Proxies_Proxy[]
     */
    public function getByBlocked($blocked, $for_save = false)
    {
        $obj = new Model_Proxies_Proxy($this);

        return parent::_getCollectionByIndex($blocked, $obj, new \Database_General(), Model_Proxies_Proxy::TABLE, Model_Proxies_Proxy::INDEX, $for_save);
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
     * @param Model_Proxies_Proxy $proxy
     *
     * @return bool|int|null
     */
    public function save(Model_Proxies_Proxy $proxy)
    {
        if ($proxy->proxyId) {
            $result = parent::_saveDifferencesByIndex($proxy->proxyId, $proxy, new \Database_General(), Model_Proxies_Proxy::TABLE);
        } else {
            $result = parent::_insert($proxy, new \Database_General(), Model_Proxies_Proxy::TABLE);

            if ($result) {
                $proxy->proxyId = $result;
            }
        }

        return $result;
    }

    public function delete(Model_Proxies_Proxy $proxy)
    {
        return parent::_deleteByIndex($proxy->proxyId, new \Database_General(), Model_Proxies_Proxy::TABLE);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        return new \Lib_ORM_Query(new Model_Proxies_Proxy($this), new \Database_General(), Model_Proxies_Proxy::TABLE);
    }
}
