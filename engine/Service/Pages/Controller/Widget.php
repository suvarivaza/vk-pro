<?php

namespace Service\Pages;

/**
 * Class Controller_Widget
 *
 * @package Service\Pages
 *
 * @property Model_Factory $factoryPages
 */
abstract class Controller_Widget extends \System\Service_Controller_Widget
{
    private $_pages = null;

    public function __get($name)
    {
        switch ($name) {
            case 'factoryPages':
                if ($this->_pages === null) {
                    $this->_pages = new Model_Factory();
                }

                return $this->_pages;
        }
        throw new \Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }
}
