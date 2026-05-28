<?php

namespace Service\Orders;

use System\HttpResponse;

class Controller_State_Client_SpryPay_Ipn extends Controller_State_Client_SpryPay
{
    public function actionGet()
    {
        return $this->_response->setStatus(HttpResponse::S4_METHOD_NOT_ALLOWED)->setBody('<h1>METHOD NOT ALLOWED</h1>');
    }

    public function actionPost()
    {
        $order = $this->factoryOrders->orders->getById($this->_request->post['spShopPaymentId']->int());

        if ($order === null) {
            return 'error';
        }

        $this->_activeOrder($order);
        echo $this->_response->setBody('ok');
        exit;
    }
}
