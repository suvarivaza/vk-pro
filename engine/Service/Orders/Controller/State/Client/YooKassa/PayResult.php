<?php

namespace Service\Orders;

use http\Exception;
use YooKassa\Client;
use YooKassa\Model\Notification\NotificationSucceeded;
use YooKassa\Model\Notification\NotificationWaitingForCapture;
use YooKassa\Model\NotificationEventType;
use YooKassa\Model\PaymentStatus;

class Controller_State_Client_YooKassa_PayResult extends Controller_State_Client_YooKassa
{
    public function actionGet()
    {

        $userId = $this->_request->get['userId']->int();
        $token = $this->_request->get['token']->string();

        if (empty($userId) or empty($token)) {
            return $this->_response->setLocation('/');
        }

        if ($userId != $this->_application->UserID) {
            return $this->_response->setLocation('/');
        }

        $query = $this->factoryOrders->orders->query()->sort('isBuyDate', 'DESC')->limit(1)->sqlCalcFoundRows(true);
        $query->filter->fieldValue('userId', '=', $userId);
        $query->filter->fieldValue('isBuy', '=', true);
        $query->filter->fieldValue('token', '=', $token);
        $it = $query->iterator();

        //если такой заказ не найден
        if ($it->getTotal() < 1) {
            return $this->_response->setLocation('/');
        }

        //если заказ с нужным токеном найден и он оплачен переводим на страницу успешной оплаты
        $order = $it->current();

        $vars = [
            'order' => $order,
            'user' => $this->_application->User,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/success', $vars));

    }


    public function actionPost()
    {

        logs(file_get_contents('php://input'), 'payment_log.txt');

        // Получим данные из POST-запроса от Яндекс.Кассы
        $source = file_get_contents('php://input');
        $requestBody = json_decode($source, true);


        // Создадим объект класса уведомлений в зависимости от события
        // NotificationSucceeded, NotificationWaitingForCapture,
        // NotificationCanceled,  NotificationRefundSucceeded

        try {
            $notification = ($requestBody['event'] === NotificationEventType::PAYMENT_SUCCEEDED)
                ? new NotificationSucceeded($requestBody)
                : new NotificationWaitingForCapture($requestBody);
        } catch (\Exception $e) {
            // Обработка ошибок при неверных данных
            mail("42-36-42@mail.ru", "Vk-pro.top - ERROR YooKassa", "При обработке платежа от YooKassa возникла ошибка: " . $e->getMessage());
            die;
        }


        // Получим объект платежа
        $payment = $notification->getObject();

        if ($payment->getStatus() === PaymentStatus::SUCCEEDED) {
            mail("42-36-42@mail.ru", "Vk-pro.top - SUCCESS PAYMENT YooKassa", "Получен платеж на сумму: " . $payment->amount->value . "<br/>" . "Детали платежа: " . $payment->description . "<br/>");
        } else {
            //заказ не оплачен
            mail("42-36-42@mail.ru", "Vk-pro.top - PAYMENT CANCELED YooKassa", print_r($payment, true));
            die;
        }


        //ИЛИ! Можно вот так коротко обработать результат платежа (оставил пока для примера) но кажется так не стоит делать - возникают ошибки
//        $notification = new NotificationSucceeded($requestBody);
//        $payment = $notification->getObject();
//        $status = $payment->status;
//        if ($status !== 'succeeded') die;


        //здесь заказ оплачен.

        $this->_response->setContentType('application/xml');

        $sum = (int)$payment->amount->value;
        $balance = (int)$payment->metadata->balance;
        $service = (string)$payment->metadata->service;
        $month = (int)$payment->metadata->month;
        $packId = (int)$payment->metadata->packId;
        $token = (string)$payment->metadata->token;

        $invoiceId = (string)$payment->id;
        $userId = (int)$payment->metadata->userId;
        $type = (string)$payment->metadata->type;
        $user = $this->factoryUsers->users->getById($userId);

        $pack = null;

        $xml = new \DOMDocument('1.0', 'utf-8');
        $checkOrderResponse = new \DOMElement('checkOrderResponse');
        $xml->appendChild($checkOrderResponse);


        if ($type == 'karmaMinus') {
            $order = $this->factoryOrders->orders->getNew();
            $order->userId = $user->userId;
            $order->type = $type;
            $order->packId = 0;
            $order->invoiceId = $invoiceId;
            $order->token = $token;
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
            $order->invoiceId = $invoiceId;
            $order->token = $token;

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

            $order->invoiceId = $invoiceId;
            $order->token = $token;
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
            $order->invoiceId = $invoiceId;
            $order->token = $token;
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

        if ($order->isBuy) {
            $checkOrderResponse->setAttribute('performedDatetime', date('c'));
            $checkOrderResponse->setAttribute('code', Model_Config::YANDEX_CODE_OK);
            $checkOrderResponse->setAttribute('shopId', shopId);
            $checkOrderResponse->setAttribute('invoiceId', $order->invoiceId);

            return $this->_response->setBody($xml->saveXML());
        }


        if ($this->_activeOrder($order)) {
            $checkOrderResponse->setAttribute('performedDatetime', date('c'));
            $checkOrderResponse->setAttribute('code', Model_Config::YANDEX_CODE_OK);
            $checkOrderResponse->setAttribute('shopId', shopId);
            $checkOrderResponse->setAttribute('invoiceId', $order->invoiceId);

            return $this->_response->setBody($xml->saveXML());
        }

//            if ($this->factoryOrders->orders->save($order)) {
//                $checkOrderResponse->setAttribute('performedDatetime', date('c'));
//                $checkOrderResponse->setAttribute('code', Model_Config::YANDEX_CODE_OK);
//                $checkOrderResponse->setAttribute('shopId', shopId);
//                $checkOrderResponse->setAttribute('invoiceId', $order->invoiceId);
//
//                return $this->_response->setBody($xml->saveXML());
//            }

        return $this->_response->setStatus(\System\HttpResponse::S5_INTERNAL_SERVER_ERROR);


        //Можно еще так обработать ответ от Юкассы
//        // Получим данные из POST-запроса от Яндекс.Кассы
//        $source = file_get_contents('php://input');
//        $requestBody = json_decode($source, true);
//
//        // Создадим объект класса уведомлений в зависимости от события
//        // NotificationSucceeded, NotificationWaitingForCapture,
//        // NotificationCanceled,  NotificationRefundSucceeded
//
//        try {
//            $notification = ($requestBody['event'] === NotificationEventType::PAYMENT_SUCCEEDED)
//                ? new NotificationSucceeded($requestBody)
//                : new NotificationWaitingForCapture($requestBody);
//        } catch (Exception $e) {
//            // Обработка ошибок при неверных данных
//        }
//
//        // Получим объект платежа
//        $payment = $notification->getObject();
//        if ($payment->getStatus() === PaymentStatus::SUCCEEDED) {
//            // Отправка сообщения
//            $mailTo = "42-36-42@mail.ru"; // Ваш e-mail
//            $subject = "На сайте vk-pro.top совершен платеж"; // Тема сообщения
//            // Сообщение
//            $message = "Платеж на сумму: " . $payment->amount->value . "<br/>";
//            $message .= "Детали платежа: " . $payment->description . "<br/>";
//
//            $headers = "MIME-Version: 1.0\r\n";
//            $headers .= "Content-type: text/html; charset=utf-8\r\n";
//            $headers .= "From: info@site.ru <info@site.ru>\r\n";
//
//            mail($mailTo, $subject, $message, $headers);
//        }


    }

}