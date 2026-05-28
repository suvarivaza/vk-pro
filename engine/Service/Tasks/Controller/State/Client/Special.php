<?php

namespace Service\Tasks;

abstract class Controller_State_Client_Special extends Controller_State_Client
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

        $this->_application->page = 'special';

        $this->_application->Title->add('link', [
            'rel' => 'icon',
            'href' => '/img/icons/32/icon-special.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->add('link', [
            'rel' => 'shortcut icon',
            'href' => '/img/icons/32/icon-special.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->Title = 'Спецзадания';

        return null;
    }
}
