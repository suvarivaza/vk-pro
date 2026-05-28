<?php

namespace Service\Api;

class Controller_State_Client_Balance extends Controller_State_Client
{
    public function actionGet()
    {
        $balance = $this->_application->User->balance;

        $vars = [
            'balance' => $balance,
        ];

        return $this->_response->setJson($vars);
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'getBalance':
                return $this->getBalance();
        }

        return $this->_response->setStatus(\System\HttpResponse::S4_BAD_REQUEST);
    }

    private function getBalance()
    {
        $list = $this->factoryUsers->users->balance->getByUserId($this->_application->UserID);
        $vars = [
            'balance' => $this->_application->User->balance,
            'list' => $list,
        ];

        return $this->_response->setJson($vars);
    }
}
