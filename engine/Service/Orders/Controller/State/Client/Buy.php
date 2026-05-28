<?php

namespace Service\Orders;

class Controller_State_Client_Buy extends Controller_State_Client
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        $this->_application->page = 'buy';

        $this->_application->Title->addScripts([
            '/js/jquery/jquery.dd.min.js',
            '/js/orders/buy.min.js',
        ]);
        $this->_application->Title->addStyle('/css/jquery/dd.min.css');
        $this->_application->Title->addStyle('/css/bower_components/font-awesome/css/font-awesome.min.css?1.2');

        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setLocation('/users/login');
        }
        $this->_application->Title->Title = 'Купить баллы';

        return null;
    }

    public function actionGet()
    {
        $query = $this->factoryOrders->orders->packs->query()->limit(100)->sort('price', 'ASC');
        $it = $query->iterator();
        $list = [];

        foreach ($it as $pack) {
            $list[] = $pack;
        }

        $query = $this->factoryOrders->orders->query()->limit(1);
        $query->filter->fieldValue('userId', '=', $this->_application->UserID)
            ->fieldValue('packId', '>', 0);
        $it = $query->iterator();
        $orders = [];

        foreach ($it as $order) {
            $orders[] = $order;
        }
        $first = false;
        $bonus = 0;

        if (!count($orders)) {
            $first = true;
            $bonusSettings = \Service\Users\Model_Config::GetBonusSettings();
            $bonus = $bonusSettings['buy'];
        }

        $giftsList = $this->factoryOrders->gifts->getByUserId($this->_application->UserID);
        $gifts = [];

        foreach ($giftsList as $id => $gift) {
            if (!$gift->isActive) {
                $gifts[] = $gift;
            }
        }

        $vars = [
            'list' => $list,
            'first' => $first,
            'bonus' => $bonus,
            'userId' => $this->_application->UserID,
            'gifts' => $gifts,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/buy', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'getPackForm':
                return $this->_getPackForm();
        }

        return parent::actionPost();
    }

    private function _getPackForm()
    {
        $settings = json_decode(file_get_contents(Model_Config::$settings), true);
        $packId = $this->_request->post['packId']->int(0);
        $pack = $this->factoryOrders->orders->packs->getById($packId);

        if ($pack === null) {
            $giftId = $this->_request->post['giftId']->int(0);

            if ($giftId > 0) {
                $gift = $this->factoryOrders->gifts->getById($giftId);

                if ($gift !== null && $gift->userId = $this->_application->UserID) {
                    $pack = $this->factoryOrders->orders->packs->getById($gift->packId);

                    $redirect = false;

                    if ($pack->serviceCount || $pack->serviceAll) {
                        $redirect = false;
                    }

                    $query = $this->factoryOrders->orders->query()->limit(1);
                    $query->filter->fieldValue('userId', '=', $this->_application->UserID)
                        ->fieldValue('packId', '>', 0);
                    $it = $query->iterator();
                    $orders = [];

                    foreach ($it as $order) {
                        $orders[] = $order;
                    }
                    $first = false;
                    $bonus = 0;

                    if (!count($orders)) {
                        $first = true;
                        $bonusSettings = \Service\Users\Model_Config::GetBonusSettings();
                        $bonus = $bonusSettings['buy'];
                    }

                    $vars = [
                        'pack' => $pack,
                        'gift' => $gift,
                        'first' => false,
                        'bonus' => 0,
                        'userId' => $this->_application->UserID,
                    ];

                    $json = [
                        'success' => true,
                        'redirect' => false,
                        'href' => '/orders/action?packId=' . $packId,
                        'limit' => $pack->serviceCount,
                        'title' => 'Ваш заказ',
                        'html' => \STPL::Fetch('client/pack_form', $vars),
                    ];

                    return $this->_response->setJson($json);
                }
            }

            $vars = [
                'settings' => $settings,
                'userId' => $this->_application->UserID,
            ];

            return $this->_response->setJson([
                'success' => true,
                'html' => \STPL::Fetch('client/balance_add_form', $vars),
            ]);
        }

        $redirect = false;

        if ($pack->serviceCount || $pack->serviceAll) {
            $redirect = false;
        }

        $query = $this->factoryOrders->orders->query()->limit(1);
        $query->filter->fieldValue('userId', '=', $this->_application->UserID)
            ->fieldValue('packId', '>', 0);
        $it = $query->iterator();
        $orders = [];

        foreach ($it as $order) {
            $orders[] = $order;
        }
        $first = false;
        $bonus = 0;

        if (!count($orders)) {
            $first = true;
            $bonusSettings = \Service\Users\Model_Config::GetBonusSettings();
            $bonus = $bonusSettings['buy'];
        }

        $vars = [
            'pack' => $pack,
            'first' => $first,
            'bonus' => $bonus,
            'settings' => $settings,
            'userId' => $this->_application->UserID,
        ];

        $json = [
            'success' => true,
            'redirect' => $redirect,
            'href' => '/orders/action?packId=' . $packId,
            'limit' => $pack->serviceCount,
            'title' => 'Ваш заказ',
            'html' => \STPL::Fetch('client/pack_form', $vars),
        ];

        return $this->_response->setJson($json);
    }
}
