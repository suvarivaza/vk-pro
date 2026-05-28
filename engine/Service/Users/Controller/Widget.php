<?php

namespace Service\Users;

/**
 * Class Controller_Widget
 *
 * @package Service\Pages
 *
 * @property Model_Factory $factory
 */
abstract class Controller_Widget extends \System\Service_Controller_Widget
{
    private $_factory = null;

    public function __get($name)
    {
        switch ($name) {
            case 'factoryUsers':
            case 'factory':
                if ($this->_factory === null) {
                    $this->_factory = new Model_Factory();
                }

                return $this->_factory;
        }
        throw new \Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }
}
