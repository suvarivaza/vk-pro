<?php

namespace Service\System;

/**
 * Class Controller_Widget
 *
 * @package Service\Catalog
 *
 * @property \Service\Catalog\Model_Factory $factoryCatalog
 * @property \Service\Pages\Model_Pages $factoryPages
 */
abstract class Controller_Widget extends \System\Service_Controller_Widget
{
    private $_factoryCatalog = null;
    private $_factoryPages = null;

    public function __get($name)
    {
        switch ($name) {
            case 'factoryCatalog':
                if ($this->_factoryCatalog === null) {
                    $this->_factoryCatalog = new \Service\Catalog\Model_Factory();
                }

                return $this->_factoryCatalog;
            case 'factoryPages':
                if ($this->_factoryPages === null) {
                    $this->_factoryPages = new \Service\Pages\Model_Pages();
                }

                return $this->_factoryPages;
        }

        throw new \Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }
}
