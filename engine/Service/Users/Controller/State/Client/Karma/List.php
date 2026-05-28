<?php

namespace Service\Users;

class Controller_State_Client_Karma_List extends Controller_State_Client
{
    public function actionPrepare()
    {
        $this->_application->userPage = 'karma';
        $this->_application->Title->Title = 'Карма';

        $this->_application->Title->add('link', [
            'rel' => 'icon',
            'href' => '/img/icons/32/icon-karma.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->add('link', [
            'rel' => 'shortcut icon',
            'href' => '/img/icons/32/icon-karma.png',
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
        $list = $this->factoryUsers->users->karma->getByUserId($this->_application->UserID);

        usort($list, [$this, '_sort_karma']);

        $vars = [
            'user' => $this->_application->User,
            'list' => $list,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/karma/list', $vars));
    }

    public function actionPost()
    {
    }

    protected function _sort_karma($a, $b)
    {
        return $a->dateCreate < $b->dateCreate;
    }
}
