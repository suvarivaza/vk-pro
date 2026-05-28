<?php

namespace Service\Bot;

/**
 * Class Controller_State_Admin
 *
 * @package Service\Faq
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

        $this->_application->page = 'bot';

        $this->_application->Title->add('link', [
            'rel' => 'icon',
            'href' => '/img/icons/32/icon-bot.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->add('link', [
            'rel' => 'shortcut icon',
            'href' => '/img/icons/32/icon-bot.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->Title = 'Автобот';

        return null;
    }
}
