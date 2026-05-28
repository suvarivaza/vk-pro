<?php

namespace Service\Pages;

/**
 * Class Controller_Client
 *
 * @package Service\Pages
 *
 * @property Model_Factory $factory
 */
abstract class Controller_Client extends \System\Service_Controller_State
{
    private $_factory = null;

    public function actionPrepare()
    {
        parent::actionPrepare();
    }

    /**
     * @return void|\System\HttpResponse
     */
    public function actionPost()
    {
        $response = new \System\HttpResponse();
        $response->setStatus(\System\HttpResponse::S4_NOT_FOUND);

        return $response;
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

        return null;
    }
}
