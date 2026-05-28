<?php

namespace Service\Orders;
use YooKassa\Client;

class Controller_State_Client_YooKassa_Pay extends Controller_State_Client_YooKassa
{

    public function actionGet()
    {

        return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
    }

    public function actionPost()
    {

        //$result = mail('42-36-42@mail.ru','Проверка отправки письма', 'Проверка отправки письма');
        $payData = $this->_request->post['payData']->value();

        if (!empty($payData) ) {

            $idempotenceKey = uniqid('', true);
            $token = uniqid('', true);
            $payData['token'] = $token;

            //создаем объект платежа YooKassa
            $client = new Client();
            $client->setAuth(ShopIdYooKassa, ApiYooKassa);
            $response = $client->createPayment(
                array(
                    'amount' => array(
                        'value' => $payData['sum'],
                        'currency' => 'RUB',
                    ),
                    'payment_method_data' => array(
                        'type' => $payData['typePay'],
                    ),
                    'confirmation' => array(
                        'type' => 'redirect',
                        'return_url' => "https://vk-pro.top/orders/pay_result?userId={$payData['userId']}&token={$token}",
                    ),
                    'capture' => true,
                    'description' => "Покупка: {$payData['orderTitle']}",
                    'metadata' => $payData
                ),
                $idempotenceKey
            );


            //отладочная информация
            //Получим информацию о созданном платеже
//            $paymentId = $response->getId();
//            $paymentInfo = $client->getPaymentInfo($paymentId);


            //редиректим пользователея на оплату
            $confirmationUrl = $response->getConfirmation()->getConfirmationUrl();
            redirect($confirmationUrl);
            die;
        }
    }

}
