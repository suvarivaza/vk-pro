<?php

namespace Service\Orders;

class Controller_State_Client_Done extends Controller_State_Client
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        $this->_application->Title->Title = 'Платеж успешно обработан';

        return null;
    }

    public function actionGet()
    {
        $orderId = $this->_request->get['orderId']->int(0);
        $order = $this->factoryOrders->orders->getById($orderId);

        if ($order === null) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }

        if ($order->userId != $this->_application->User->userId) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }

        $vars = [
            'order' => $order,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/done', $vars));
    }

    public function actionPost()
    {
        // TODO: Implement actionPost() method.
    }
}
