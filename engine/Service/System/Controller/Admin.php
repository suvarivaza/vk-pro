<?php

namespace Service\System;

/**
 * Class Controller_Admin
 *
 * @package Service\System
 *
 * @property Model_Factory $factory
 */
abstract class Controller_Admin extends \System\Service_Controller_State
{
    private $_factory = null;

    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if (null !== $response) {
            return $response;
        }

        return null;
    }

    public function __get($name)
    {
        switch ($name) {
            case 'factory':
                if ($this->_factory === null) {
                    $this->_factory = new Model_Factory();
                }

                return $this->_factory;
        }

        throw new \Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }
}
