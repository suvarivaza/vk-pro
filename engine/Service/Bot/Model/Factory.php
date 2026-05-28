<?php

namespace Service\Bot;

use Database_Main;
use Lib_DB_Adapter;
use Lib_DB_Factory;
use Lib_Exception_UnknownProperty_Backtraced;

/**
 * Class Model_Factory
 *
 * @package Service\Bot
 *
 * @property Model_Bots $bots
 * @property Lib_DB_Adapter $db
 */
class Model_Factory
{
    protected $_bots = null;
    private $_db = null;

    public function __get($name)
    {
        switch ($name) {
            case 'bots':
                if ($this->_bots === null) {
                    $this->_bots = new Model_Bots($this);
                }

                return $this->_bots;
            case 'db':
                if (null === $this->_db) {
                    $this->_db = Lib_DB_Factory::GetInstance(new Database_Main());
                }

                return $this->_db;
        }
        throw new Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }
}
