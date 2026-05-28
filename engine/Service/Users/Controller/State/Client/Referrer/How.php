<?php

namespace Service\Users;

class Controller_State_Client_Referrer_How extends Controller_State_Client
{
    public function actionPrepare()
    {
        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setLocation('/users/login');
        }

        $this->_application->userPage = 'referrer';
        $this->_application->Title->Title = 'Партнерская программа';

        $this->_application->Title->add('link', [
            'rel' => 'icon',
            'href' => '/img/icons/32/icon-referrer.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->add('link', [
            'rel' => 'shortcut icon',
            'href' => '/img/icons/32/icon-referrer.png',
            'type' => 'image/png',
        ]);

        $reponse = parent::actionPrepare();

        if ($reponse !== null) {
            return $reponse;
        }

        return null;
    }

    public function actionGet()
    {
        $vars = [
            'user' => $this->_application->User,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/referrer/how', $vars));
    }

    public function actionPost()
    {
        return parent::actionPost();
    }
}
