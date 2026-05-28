<?php

namespace Service\Orders;

class Controller_State_Client_Yandex_Error extends Controller_State_Client_Yandex
{
    public function actionGet()
    {
        $invoiceId = $this->_request->get['invoiceId']->string('');
        $order = $this->factoryOrders->orders->getByUserIdInvoiceId($this->_application->UserID, $invoiceId);

        $vars = [
            'order' => $order,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/error', $vars));
    }

    public function actionPost()
    {
        return $this->_response->setStatus(\System\HttpResponse::S4_METHOD_NOT_ALLOWED);
    }
}
