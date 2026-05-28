<?php

namespace Service\Users;

/**
 * Class Controller_State
 *
 * @package Service\Users
 *
 * @property Model_Factory $factory
 */
abstract class Controller_State extends \System\Service_Controller_State
{
    protected $_limit = 20;
    private $_factory = null;

    public function actionPrepare()
    {
        return parent::actionPrepare();
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

        return parent::__get($name);
    }
}
