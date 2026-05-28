<?php

namespace Service\Orders;

class Controller_State_Client_Order extends Controller_State_Client
{
    public function actionGet()
    {
        return $this->_response->setLocation('/orders/buy');
    }

    public function actionPost()
    {

        $sum = $this->_request->post['sum']->dec(0.0);

        $balance = $this->_request->post['balance']->int(0);
        $service = $this->_request->post['service']->enum(null, ['auto', 'posting', 'grabber', 'special', 'bot']);
        $month = $this->_request->post['month']->int(0);
        $packId = $this->_request->post['packId']->int(0);
        $group = $this->_request->post['group']->bool(false);
        $type = $this->_request->post['type']->string('');
        $giftId = $this->_request->post['giftId']->int(0);



        if ($giftId > 0) {
            $gift = $this->factoryOrders->gifts->getById($giftId, true);

            if ($gift !== null && $gift->userId != $this->_application->UserID) {
                $gift = null;
            }

            if ($gift !== null && $gift->isActive) {
                $gift = null;
            }
        }

        $arr = [
            'sum' => $sum,
            'balance' => $balance,
            'service' => $service,
            'month' => $month,
            'packId' => $packId,
            'group' => $group,
            'type' => $type,
            'service1' => $this->_request->post['service1']->enum(null,
                ['auto', 'posting', 'grabber', 'special', 'bot']),
            'service2' => $this->_request->post['service2']->enum(null,
                ['auto', 'posting', 'grabber', 'special', 'bot']),
            'service3' => $this->_request->post['service3']->enum(null,
                ['auto', 'posting', 'grabber', 'special', 'bot']),
            'service4' => $this->_request->post['service4']->enum(null,
                ['auto', 'posting', 'grabber', 'special', 'bot']),
            'service5' => $this->_request->post['service4']->enum(null,
                ['auto', 'posting', 'grabber', 'special', 'bot']),
        ];

        $title = '';

        if ($type == 'karmaMinus') {
            $title = 'Восстановление кармы';

            $order = $this->factoryOrders->orders->getNew();
            $order->userId = $this->_application->User->userId;
            $order->type = $type;
            $order->packId = 0;
            $order->invoiceId = $this->_request->post['invoiceId']->string('');
            $order->price = (float)($this->_application->User->getKarmaPrice());

            if ($order->price != $sum) {
                $this->_errors[] = 'Указанная сумма не правильна';

                return $this->_response->setBody(\STPL::Fetch('client/order', [
                    'errors' => $this->_errors,
                ]));
            }

            $order->balance = 0;
            $order->dateCreate = time();

            $order->isReferrer = false;

            return $this->_response->setBody(\STPL::Fetch('client/order', [
                'order' => $order,
                'title' => $title,
                'user' => $this->_application->User,
                'arr' => $arr,
                'errors' => $this->_errors,
            ]));
        }


        if (!$packId && !$balance && !$service) {
            $this->_errors[] = 'Не правильные параметры';

            return $this->_response->setBody(\STPL::Fetch('client/order', [
                'errors' => $this->_errors,
            ]));
        }

        if ($packId > 0) {
            $pack = $this->factoryOrders->orders->packs->getById($packId);

            if ($pack === null) {
                $this->_errors[] = 'Не правильные параметры1';

                return $this->_response->setBody(\STPL::Fetch('client/order', [
                    'errors' => $this->_errors,
                ]));
            }

            if ($pack->price != $sum) {
                $this->_errors[] = 'Не правильные параметры' . $pack->price . '!=' . $sum;

                return $this->_response->setBody(\STPL::Fetch('client/order', [
                    'errors' => $this->_errors,
                ]));
            }
        } elseif ($balance > 0) {
            $settings = json_decode(file_get_contents(Model_Config::$settings), true);
            $price = ($balance / 10) * $settings['balance']['price'];

            if ($price != $sum) {
                $this->_errors[] = 'Не правильные параметры';

                return $this->_response->setBody(\STPL::Fetch('client/order', [
                    'errors' => $this->_errors,
                ]));
            }
        } elseif ($service != null) {
            $settings = json_decode(file_get_contents(Model_Config::$settings), true);
            $price = floatval($settings[$service]['groups'][$month]);

            if ($price != $sum) {
                $this->_errors[] = 'Не правильные параметры';

                return $this->_response->setBody(\STPL::Fetch('client/order', [
                    'errors' => $this->_errors,
                ]));
            }
        }



        if ($packId > 0) {
            $query = $this->factoryOrders->orders->query()->limit(1);
            $query->filter->fieldValue('userId', '=', $this->_application->User->userId)
                ->fieldValue('packId', '>', 0);
            $it = $query->iterator();
            $orders = [];

            foreach ($it as $order) {
                $orders[] = $order;
            }
            $first = false;

            if (!count($orders)) {
                $first = true;
            }

            $pack = $this->factoryOrders->orders->packs->getById($packId);

            if (!$pack) {
                return $this->_response->setLocation('/orders/buy');
            }

            $title = 'Пакет ' . $pack->title;

            $order = $this->factoryOrders->orders->getNew();
            $order->userId = $this->_application->User->userId;
            $order->packId = $pack->packId;
            $order->invoiceId = $this->_request->post['invoiceId']->string('');

            if ($first) {
                $order->balance = $pack->balance + $pack->balance;
            } else {
                $order->balance = $pack->balance + $pack->bonus;
            }

            $order->price = (float)$pack->price;

            $order->isReferrer = $pack->isReferrer;

            $i = 0;

            if ($pack->serviceAll) {
                $order->isAuto = true;
                $order->isPosting = true;
                $order->isGrabber = true;
                $order->isSpecial = true;
                $order->isBot = true;

                $order->isAutoMonth = $pack->serviceMonth;
                $order->isPostingMonth = $pack->serviceMonth;
                $order->isGrabberMonth = $pack->serviceMonth;
                $order->isSpecialMonth = $pack->serviceMonth;
                $order->isBotMonth = $pack->serviceMonth;
            }

            for ($i = 0; $i < $pack->serviceCount; $i++) {
                $service = $this->_request->post['service' . ($i + 1)]->enum(null,
                    ['auto', 'posting', 'grabber', 'special']);

                if ($service == 'auto') {
                    $order->isAuto = true;
                    $order->isAutoMonth += $pack->serviceMonth;
                } elseif ($service == 'posting') {
                    $order->isPosting = true;
                    $order->isPostingMonth += $pack->serviceMonth;
                } elseif ($service == 'grabber') {
                    $order->isGrabber = true;
                    $order->isGrabberMonth += $pack->serviceMonth;
                } elseif ($service == 'special') {
                    $order->isSpecial = true;
                    $order->isSpecialMonth += $pack->serviceMonth;
                } elseif ($service == 'bot') {
                    $order->isBot = true;
                    $order->isBotMonth += $pack->serviceMonth;
                }
            }
        } elseif ($balance > 0) {

            $settings = json_decode(file_get_contents(Model_Config::$settings), true);
            $order = $this->factoryOrders->orders->getNew();
            $order->userId = $this->_application->User->userId;
            $order->invoiceId = $this->_request->post['invoiceId']->string('');
            $order->packId = 0;
            $order->balance = $balance;
            $order->token = '';
            //$price = floatval(($balance / 10) * $settings['balance']['price']);
            $order->price = (float)(($balance / 10) * $settings['balance']['price']);

            //var_dump($order); die;

            //$this->factoryOrders->orders->save($order);
            $title = 'Покупка баллов в количестве ' . $order->balance;
        } elseif ($service != null) {

            $settings = json_decode(file_get_contents(Model_Config::$settings), true);
            $order = $this->factoryOrders->orders->getNew();
            $order->userId = $this->_application->User->userId;
            $order->invoiceId = $this->_request->post['invoiceId']->string('');
            $order->packId = 0;
            $order->balance = 0;
            $order->price = (float)($settings[$service]['groups'][$month]);
            $order->isSlot = true;
            $order->monthId = $month;

            switch ($service) {
                case 'auto':
                    $title = 'Активация сервиса Автоведение';
                    $order->isAuto = true;
                    $order->isAutoMonth = intval($settings[$service]['months'][$month]);
                    break;
                case 'posting':
                    $title = 'Активация сервиса Автопостинг';
                    $order->isPosting = true;
                    $order->isPostingMonth = intval($settings[$service]['months'][$month]);
                    break;
                case 'grabber':
                    $title = 'Активация сервиса Граббер';
                    $order->isGrabber = true;
                    $order->isGrabberMonth = intval($settings[$service]['months'][$month]);
                    break;
                case 'special':
                    $title = 'Активация сервиса Спецзадания';
                    $order->isSpecial = true;
                    $order->isSpecialMonth = intval($settings[$service]['months'][$month]);
                    break;
                case 'bot':
                    $title = 'Активация сервиса Автобот PRO';
                    $order->isBot = true;
                    $order->isBotMonth = intval($settings[$service]['months'][$month]);
                    break;
            }
        } else {

            $this->_errors[] = 'Не правильные параметры';

            return $this->_response->setBody(\STPL::Fetch('client/order', [
                'errors' => $this->_errors,
            ]));
        }



        if (isset($gift)) {
            $order->giftId = $gift->giftId;
            $order->isReferrer = false;

            if ($this->_activeOrder($order)) {
                $gift->isActive = true;
                $gift->isActiveDate = time();
                $this->factoryOrders->gifts->save($gift);

                return $this->_response->setLocation('/orders/success?customerNumber=' . $this->_application->UserID);
            } else {
                return $this->_response->setLocation('/orders/error');
            }
        }

        return $this->_response->setBody(\STPL::Fetch('client/order', [
            'title' => $title,
            'order' => $order,
            'pack' => $pack,
            'first' => $first,
            'user' => $this->_application->User,
            'arr' => $arr,
        ]));
    }
}
