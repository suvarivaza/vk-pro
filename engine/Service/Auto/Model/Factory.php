<?php

namespace Service\Auto;

use Database_Main;
use Lib_DB_Adapter;
use Lib_DB_Factory;
use Lib_Exception_UnknownProperty_Backtraced;

/**
 * Class Model_Factory
 *
 * @package Service\Auto
 *
 * @property Model_Autos $auto
 * @property Lib_DB_Adapter $db
 */
class Model_Factory
{
    private $_auto = null;
    private $_db = null;

    public function __get($name)
    {
        switch ($name) {
            case 'auto':
                if (null === $this->_auto) {
                    $this->_auto = new Model_Autos($this);
                }

                return $this->_auto;
            case 'db':
                if (null === $this->_db) {
                    $this->_db = Lib_DB_Factory::GetInstance(new Database_Main());
                }

                return $this->_db;
        }

        throw new Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }
}
