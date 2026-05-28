<?php

namespace Service\Users;

class Controller_State_Client_Buy extends Controller_State_Client
{
    public function actionPrepare()
    {
        $this->_application->userPage = 'buy';
        $this->_application->Title->Title = 'История покупок';

        $this->_application->Title->add('link', [
            'rel' => 'icon',
            'href' => '/img/icons/32/icon-buy.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->add('link', [
            'rel' => 'shortcut icon',
            'href' => '/img/icons/32/icon-buy.png',
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
        $query = $this->factoryOrders->orders->packs->query()->limit(100)->sort('price', 'ASC');
        $it = $query->iterator();
        $packs = [];

        foreach ($it as $pack) {
            $packs[$pack->packId] = $pack;
        }

        $page = $this->_request->get['p']->int(1);
        $query = $this->factoryOrders->orders->query()->limit(10)->offset(($page - 1) * 10)->sort('dateCreate',
            'DESC')->sqlCalcFoundRows(true);
        $query->filter->fieldValue('userId', '=', $this->_application->UserID);

        $it = $query->iterator();

        $vars = [
            'user' => $this->_application->User,
            'list' => $it,
            'packs' => $packs,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/buy', $vars));
    }

    public function actionPost()
    {
    }
}
