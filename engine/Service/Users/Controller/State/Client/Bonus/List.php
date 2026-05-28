<?php

namespace Service\Users;

class Controller_State_Client_Bonus_List extends Controller_State_Client
{
    public function actionPrepare()
    {
        $this->_application->userPage = 'bonus';
        $this->_application->Title->Title = 'Бонусы';

        $this->_application->Title->add('link', [
            'rel' => 'icon',
            'href' => '/img/icons/32/icon-bonus.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->add('link', [
            'rel' => 'shortcut icon',
            'href' => '/img/icons/32/icon-bonus.png',
            'type' => 'image/png',
        ]);

        $reponse = parent::actionPrepare();

        if ($reponse !== null) {
            return $reponse;
        }

        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setLocation('/users/login');
        }

        return null;
    }

    public function actionGet()
    {
        $bonuses = $this->factoryUsers->users->balance->getByUserIdIsBonus($this->_application->UserID, true);

        $vars = [
            'user' => $this->_application->User,
            'list' => $bonuses,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/bonus/list', $vars));
    }

    public function actionPost()
    {
    }
}
