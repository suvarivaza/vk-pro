<?php

namespace Service\Orders;

/**
 * Class Model_Factory
 *
 * @package Service\Auto
 *
 * @property Model_Orders $orders
 * @property Model_Gifts $gifts
 * @property \Lib_DB_Adapter $db
 */
class Model_Factory
{
    private $_orders = null;
    private $_gifts = null;
    private $_db = null;

    public function __get($name)
    {
        switch ($name) {
            case 'orders':
                if (null === $this->_orders) {
                    $this->_orders = new Model_Orders($this);
                }

                return $this->_orders;
            case 'gifts':
                if (null === $this->_gifts) {
                    $this->_gifts = new Model_Gifts($this);
                }

                return $this->_gifts;
            case 'db':
                if (null === $this->_db) {
                    $this->_db = \Lib_DB_Factory::GetInstance(new \Database_Main());
                }

                return $this->_db;
        }

        throw new \Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }
}
