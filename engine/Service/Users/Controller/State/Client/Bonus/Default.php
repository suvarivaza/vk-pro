<?php

namespace Service\Users;

class Controller_State_Client_Bonus_Default extends Controller_State_Client
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
        $statistic = $this->factoryTasks->users->getStatisticByUserId($this->_application->UserID, time());

        $year = date('Y');
        $week = date('W');

        $arr = [];

        for ($i = 0; $i < 8; $i++) {
            $day = $this->factoryUsers->users->bonuses->getByUserId($this->_application->UserID, $year, $week, $i);

            if ($day === null) {
                $active = false;
            } else {
                $active = true;
            }

            $arr[$i] = [
                'title' => Model_Config::$days[$i],
                'active' => $active,
            ];
        }

        $vars = [
            'user' => $this->_application->User,
            'statistic' => $statistic,
            'types' => Model_Config::$types,
            'bonus' => Model_Config::GetBonusSettings(),
            'week' => $arr,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/bonus/default', $vars));
    }

    public function actionPost()
    {
    }
}
