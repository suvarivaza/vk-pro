<?php

namespace Service\Users;

abstract class Controller_State_Client extends Controller_State
{
    public function actionPrepare()
    {
        $this->_application->page = 'users';

        if (!$this->_application->page) {
            if (!$this->_application->userPage) {
                $this->_application->Title->add('link', [
                    'rel' => 'icon',
                    'href' => '/img/icons/32/icon-profile.png',
                    'type' => 'image/png',
                ]);

                $this->_application->Title->add('link', [
                    'rel' => 'shortcut icon',
                    'href' => '/img/icons/32/icon-profile.png',
                    'type' => 'image/png',
                ]);

                $this->_application->Title->Title = 'Профиль пользователя';
            }
        }

        return parent::actionPrepare();
    }
}
