<?php

namespace Service\Orders;

class Controller_State_Client_Yandex_Check extends Controller_State_Client_Yandex
{
    public function actionGet()
    {
        return $this->_response->setStatus(\System\HttpResponse::S4_METHOD_NOT_ALLOWED);
    }

    public function actionPost()
    {
        file_put_contents(ENGINE_PATH . 'engine/Service/Orders/Model/Post.txt', print_r($_POST, true));

        $this->_response->setContentType('application/xml');

        $sum = $this->_request->post['orderSumAmount']->dec(0.0);
        $balance = $this->_request->post['balance']->int(0);
        $service = $this->_request->post['service']->enum(null, ['auto', 'posting', 'grabber', 'special', 'bot']);
        $month = $this->_request->post['month']->int(0);
        $packId = $this->_request->post['packId']->int(0);

        $md5 = $this->_request->post['md5']->string('');
        $invoiceId = $this->_request->post['invoiceId']->string('');
        $userId = $this->_request->post['customerNumber']->int(0);
        $user = $this->factoryUsers->users->getById($userId);
        $type = $this->_request->post['type']->string('');

        $arr = [
            $this->_request->post['action']->string(''),
            $this->_request->post['orderSumAmount']->string(''),
            $this->_request->post['orderSumCurrencyPaycash']->string(''),
            $this->_request->post['orderSumBankPaycash']->string(''),
            shopId,
            $this->_request->post['invoiceId']->string(''),
            $this->_request->post['customerNumber']->string(''),
            shopPassword,
        ];
        $pack = null;

        $xml = new \DOMDocument('1.0', 'utf-8');
        $checkOrderResponse = new \DOMElement('checkOrderResponse');
        $xml->appendChild($checkOrderResponse);

        $md5Check = strtoupper(md5(implode(';', $arr)));

        if ($md5 != $md5Check || $user === null) {
            $checkOrderResponse->setAttribute('performedDatetime', date('c'));
            $checkOrderResponse->setAttribute('code', Model_Config::YANDEX_CODE_ERR);
            $checkOrderResponse->setAttribute('shopId', shopId);
            $checkOrderResponse->setAttribute('invoiceId', $invoiceId);

            return $this->_response->setBody($xml->saveXML());
        }

        if ($type == 'karmaMinus') {
            $order = $this->factoryOrders->orders->getNew();
            $order->userId = $user->userId;
            $order->type = $type;
            $order->packId = 0;
            $order->invoiceId = $this->_request->post['invoiceId']->string('');
            $order->price = (float)($user->getKarmaPrice());

            if ($order->price != $sum) {
                $checkOrderResponse->setAttribute('performedDatetime', date('c'));
                $checkOrderResponse->setAttribute('code', Model_Config::YANDEX_CODE_PARAMS);
                $checkOrderResponse->setAttribute('shopId', shopId);
                $checkOrderResponse->setAttribute('invoiceId', $invoiceId);

                return $this->_response->setBody($xml->saveXML());
            }

            $order->isOrdered = false;
            $order->isAuto = false;
            $order->isPosting = false;
            $order->isGrabber = false;
            $order->isSpecial = false;

            $order->isAutoMonth = 0;
            $order->isPostingMonth = 0;
            $order->isGrabberMonth = 0;
            $order->isSpecialMonth = 0;

            $order->balance = 0;
            $order->dateCreate = time();

            $order->isReferrer = false;

            if ($this->factoryOrders->orders->save($order)) {
                $checkOrderResponse->setAttribute('performedDatetime', date('c'));
                $checkOrderResponse->setAttribute('code', Model_Config::YANDEX_CODE_OK);
                $checkOrderResponse->setAttribute('shopId', shopId);
                $checkOrderResponse->setAttribute('invoiceId', $order->invoiceId);

                return $this->_response->setBody($xml->saveXML());
            } else {
                $checkOrderResponse->setAttribute('performedDatetime', date('c'));
                $checkOrderResponse->setAttribute('code', Model_Config::YANDEX_CODE_ERR);
                $checkOrderResponse->setAttribute('shopId', shopId);
                $checkOrderResponse->setAttribute('invoiceId', $order->invoiceId);

                return $this->_response->setBody($xml->saveXML());
            }
        }

        if (!$packId && !$balance && !$service) {
            $checkOrderResponse->setAttribute('performedDatetime', date('c'));
            $checkOrderResponse->setAttribute('code', Model_Config::YANDEX_CODE_PARAMS);
            $checkOrderResponse->setAttribute('shopId', shopId);
            $checkOrderResponse->setAttribute('invoiceId', $invoiceId);

            return $this->_response->setBody($xml->saveXML());
        }

        if ($packId > 0) {
            $pack = $this->factoryOrders->orders->packs->getById($packId);

            if ($pack === null) {
                $checkOrderResponse->setAttribute('performedDatetime', date('c'));
                $checkOrderResponse->setAttribute('code', Model_Config::YANDEX_CODE_NO_ORDER);
                $checkOrderResponse->setAttribute('shopId', shopId);
                $checkOrderResponse->setAttribute('invoiceId', $invoiceId);

                return $this->_response->setBody($xml->saveXML());
            }

            if ($pack->price != $sum) {
                $checkOrderResponse->setAttribute('performedDatetime', date('c'));
                $checkOrderResponse->setAttribute('code', Model_Config::YANDEX_CODE_PARAMS);
                $checkOrderResponse->setAttribute('shopId', shopId);
                $checkOrderResponse->setAttribute('invoiceId', $invoiceId);

                return $this->_response->setBody($xml->saveXML());
            }
        } elseif ($balance > 0) {
            $settings = json_decode(file_get_contents(Model_Config::$settings), true);
            $price = ($balance / 10) * $settings['balance']['price'];

            if ($price != $sum) {
                $checkOrderResponse->setAttribute('performedDatetime', date('c'));
                $checkOrderResponse->setAttribute('code', Model_Config::YANDEX_CODE_PARAMS);
                $checkOrderResponse->setAttribute('shopId', shopId);
                $checkOrderResponse->setAttribute('invoiceId', $invoiceId);

                return $this->_response->setBody($xml->saveXML());
            }
        } elseif ($service != null) {
            $settings = json_decode(file_get_contents(Model_Config::$settings), true);

            $price = floatval($settings[$service]['groups'][$month]);

            if ($price != $sum) {
                $checkOrderResponse->setAttribute('performedDatetime', date('c'));
                $checkOrderResponse->setAttribute('code', Model_Config::YANDEX_CODE_PARAMS);
                $checkOrderResponse->setAttribute('shopId', shopId);
                $checkOrderResponse->setAttribute('invoiceId', $invoiceId);

                return $this->_response->setBody($xml->saveXML());
            }
        }

        if ($packId > 0) {
            $query = $this->factoryOrders->orders->query()->limit(1);
            $query->filter->fieldValue('userId', '=', $user->userId)
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

            $order = $this->factoryOrders->orders->getNew();
            $order->userId = $user->userId;
            $order->packId = $pack->packId;
            $order->invoiceId = $this->_request->post['invoiceId']->string('');

            if ($first) {
                $order->balance = $pack->balance + $pack->balance;
            } else {
                $order->balance = $pack->balance + $pack->bonus;
            }

            $order->price = (float)$pack->price;

            $order->isOrdered = false;
            $order->isAuto = false;
            $order->isPosting = false;
            $order->isGrabber = false;
            $order->isSpecial = false;

            $order->isAutoMonth = 0;
            $order->isPostingMonth = 0;
            $order->isGrabberMonth = 0;
            $order->isSpecialMonth = 0;

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
                    ['auto', 'posting', 'grabber', 'special', 'bot']);

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
            $order->userId = $user->userId;
            $order->invoiceId = $this->_request->post['invoiceId']->string('');
            $order->packId = 0;
            $order->balance = $balance;
            $order->price = (float)(($balance / 10) * $settings['balance']['price']);

            $order->isOrdered = false;
            $order->isAuto = false;
            $order->isPosting = false;
            $order->isGrabber = false;
            $order->isSpecial = false;

            $order->isAutoMonth = 0;
            $order->isPostingMonth = 0;
            $order->isGrabberMonth = 0;
            $order->isSpecialMonth = 0;
        } elseif ($service != null) {
            $settings = json_decode(file_get_contents(Model_Config::$settings), true);

            $order = $this->factoryOrders->orders->getNew();
            $order->userId = $user->userId;
            $order->invoiceId = $this->_request->post['invoiceId']->string('');
            $order->packId = 0;
            $order->balance = 0;
            $order->price = (float)($settings[$service]['groups'][$month]);

            $order->isOrdered = false;
            $order->isAuto = false;
            $order->isPosting = false;
            $order->isGrabber = false;
            $order->isSpecial = false;

            $order->isAutoMonth = 0;
            $order->isPostingMonth = 0;
            $order->isGrabberMonth = 0;
            $order->isSpecialMonth = 0;
            $order->monthId = $month;

            switch ($service) {
                case 'auto':
                    $order->isAuto = true;
                    $order->isAutoMonth = intval($settings[$service]['months'][$month]);
                    break;
                case 'posting':
                    $order->isPosting = true;
                    $order->isPostingMonth = intval($settings[$service]['months'][$month]);
                    break;
                case 'grabber':
                    $order->isGrabber = true;
                    $order->isGrabberMonth = intval($settings[$service]['months'][$month]);
                    break;
                case 'special':
                    $order->isSpecial = true;
                    $order->isSpecialMonth = intval($settings[$service]['months'][$month]);
                    break;
                case 'bot':
                    $order->isBot = true;
                    $order->isBotMonth = intval($settings[$service]['months'][$month]);
                    break;
            }
        } else {
            $checkOrderResponse->setAttribute('performedDatetime', date('c'));
            $checkOrderResponse->setAttribute('code', Model_Config::YANDEX_CODE_PARAMS);
            $checkOrderResponse->setAttribute('shopId', shopId);
            $checkOrderResponse->setAttribute('invoiceId', $invoiceId);

            return $this->_response->setBody($xml->saveXML());
        }

        if ($this->factoryOrders->orders->save($order)) {
            $checkOrderResponse->setAttribute('performedDatetime', date('c'));
            $checkOrderResponse->setAttribute('code', Model_Config::YANDEX_CODE_OK);
            $checkOrderResponse->setAttribute('shopId', shopId);
            $checkOrderResponse->setAttribute('invoiceId', $order->invoiceId);

            return $this->_response->setBody($xml->saveXML());
        }

        return $this->_response->setStatus(\System\HttpResponse::S5_INTERNAL_SERVER_ERROR);
    }
}
