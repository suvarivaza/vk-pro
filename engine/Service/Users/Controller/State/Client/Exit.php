<?php

namespace Service\Users;

class Controller_State_Client_Exit extends Controller_State_Client
{
    public function actionGet()
    {
        $this->_application->delUserFromCookie();

        return $this->_response->setLocation('/');
    }

    public function actionPost()
    {
        return null;
    }
}
