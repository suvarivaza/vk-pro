<?php

namespace Service\Users;

class Controller_State_Client_Me extends Controller_State_Client
{
    private $_success = null;

    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setLocation('/users/login');
        }
    }

    public function actionGet()
    {
        $vars = [
            'success' => $this->_success,
            'user' => $this->_application->User,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/me', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'user':
                return $this->_userSave();
        }
    }

    private function _userSave()
    {
        $access_token = $this->_request->post['access_token']->string();

        if (strpos($access_token, '&') > 0) {
            $arr = explode('&', $access_token);

            foreach ($arr as $string) {
                if (preg_match('@access_token=(.*)@', $string, $matches)) {
                    $access_token = $matches[1];
                }
            }
        }

        if (preg_match('@access_token=(.*)@', $access_token, $matches)) {
            $access_token = $matches[1];
        }
        $this->_application->User->makeShadow();
        $this->_application->User->access_token = $access_token;
        $this->factoryUsers->users->save($this->_application->User);

        $this->_success = true;

        return null;
    }
}
