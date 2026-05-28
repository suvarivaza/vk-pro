<?php

namespace Service\Auto;

/**
 * Class Controller_State_Client
 *
 * @package Service\Auto
 */
abstract class Controller_State_Client extends \System\Service_Controller_State
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setLocation('/users/login');
        }

        if ($this->_application->User->login == '') {
            return $this->_response->setLocation('/users/register');
        }

        $this->_application->page = 'auto';

        $this->_application->Title->add('link', [
            'rel' => 'icon',
            'href' => '/img/icons/32/icon-auto.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->add('link', [
            'rel' => 'shortcut icon',
            'href' => '/img/icons/32/icon-auto.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->Title = 'Автоведение';

        $this->_application->Title->addScript('/js/auto.min.js');
    }
}
