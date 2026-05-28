<?php

namespace Service\Logs;

use System\Service_Controller_State;

/**
 * Class Controller_State
 *
 * @package Service\Users
 *
 * @property Model_Factory $factory
 */
abstract class Controller_State extends Service_Controller_State
{
    public const NO_CACHE = 12;
    protected $_limit = 10;
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
