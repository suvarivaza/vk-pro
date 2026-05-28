<?php

namespace Service\Orders;

class Controller_State_Client_Yandex_Success extends Controller_State_Client_Yandex
{
    public function actionGet()
    {
        $userId = $this->_request->get['customerNumber']->int(0);

        if ($userId != $this->_application->UserID) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }

        $query = $this->factoryOrders->orders->query()->sort('isBuyDate', 'DESC')->limit(1)->sqlCalcFoundRows(true);
        $query->filter->fieldValue('userId', '=', $userId);
        $query->filter->fieldValue('isBuy', '=', true);
        $it = $query->iterator();

        if ($it->getTotal() < 1) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }

        $order = $it->current();

        $vars = [
            'order' => $order,
            'user' => $this->_application->User,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/success', $vars));
    }

    public function actionPost()
    {
        return $this->_response->setStatus(\System\HttpResponse::S4_METHOD_NOT_ALLOWED);
    }
}
