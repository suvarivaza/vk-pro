<?php

namespace Service\Users;

class Controller_State_Client_Confirm extends Controller_State_Client
{
    public function actionGet()
    {
        $token = $this->_request->get['token']->string('');
        $user = $this->factoryUsers->users->getByToken($token, true);

        if ($user === null) {
            $vars['success'] = false;

            return $this->_response->setBody(\STPL::Fetch('client/confirmed', $vars));
        }

        $user->confirmed = 1;
        $this->factoryUsers->users->save($user);

        $vars = [
            'success' => true,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/confirmed', $vars));
    }

    public function actionPost()
    {
    }
}
