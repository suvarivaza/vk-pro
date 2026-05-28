<?php

namespace Service\Logs;

use Database_Logs;
use Lib_DB_Adapter;
use Lib_DB_Factory;
use Lib_Exception_UnknownProperty_Backtraced;
use Memcache;

/**
 * Class Model_Factory
 *
 * @package Service
 *
 * @property Lib_DB_Adapter $db
 * @property Memcache $cache
 * @property Model_Logs $logs
 */
class Model_Factory
{
    private $_db = null;

    private $_logs = [];

    private $_memcache = null;

    /**
     * @param int $date
     *
     * @return Model_Logs
     */
    public function getLogs($date = 0)
    {

        if (!$date) {
            $date = time();
        }

        $id = date('Y', $date) . '_' . date('m', $date);

        if (!isset($this->_logs[$id])) {
            $this->_logs[$id] = new Model_Logs($this, $date);
        }

        return $this->_logs[$id];
    }

    public function __get($name)
    {

        switch ($name) {
            case 'db':
                if (null === $this->_db) {
                    $this->_db = Lib_DB_Factory::GetInstance(new Database_Logs());
                }

                return $this->_db;

            case 'cache':
                if (null === $this->_memcache) {
                    $this->_memcache = new Memcache();
                    $this->_memcache->pconnect('localhost', 11211);
                }

                return $this->_memcache;
        }

        throw new Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }
}
