<?php

namespace Service\Orders;

class Controller_State_Client_Yandex_Aviso extends Controller_State_Client_Yandex
{
    public function actionGet()
    {
        return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
    }

    public function actionPost()
    {
        file_put_contents(ENGINE_PATH . 'engine/Service/Orders/Model/PostAviso.txt', print_r($_POST, true));

        $this->_response->setContentType('application/xml');

        $md5 = $this->_request->post['md5']->string('');
        $invoiceId = $this->_request->post['invoiceId']->string('');
        $userId = $this->_request->post['customerNumber']->int(0);
        $type = $this->_request->post['type']->string('');
        $user = $this->factoryUsers->users->getById($userId);
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

        $xml = new \DOMDocument('1.0', 'utf-8');
        $checkOrderResponse = new \DOMElement('paymentAvisoResponse');
        $xml->appendChild($checkOrderResponse);

        $md5Check = strtoupper(md5(implode(';', $arr)));

        if ($md5 != $md5Check || $user === null) {
            $checkOrderResponse->setAttribute('performedDatetime', date('c'));
            $checkOrderResponse->setAttribute('code', Model_Config::YANDEX_CODE_ERR);
            $checkOrderResponse->setAttribute('shopId', shopId);
            $checkOrderResponse->setAttribute('invoiceId', $invoiceId);

            return $this->_response->setBody($xml->saveXML());
        }

        $order = $this->factoryOrders->orders->getByUserIdInvoiceId($user->userId, $invoiceId, true);

        if ($order === null) {
            $checkOrderResponse->setAttribute('performedDatetime', date('c'));
            $checkOrderResponse->setAttribute('code', Model_Config::YANDEX_CODE_ERR);
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

        return $this->_response->setStatus(\System\HttpResponse::S5_INTERNAL_SERVER_ERROR);
    }
}
