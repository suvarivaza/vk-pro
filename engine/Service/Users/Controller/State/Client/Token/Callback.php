<?php

namespace Service\Users;

//данный коллбек используется для получения токена автоматически методом Implicit Flow

class Controller_State_Client_Token_Callback extends Controller_State_Client
{
    public function actionGet()
    {

        if ($this->_request->get['access_token']->string('') != '') {
            $access_token = $this->_request->get['access_token']->string();
            $user_id = $this->_request->get['user_id']->string();
            $expires_in = $this->_request->get['expires_in']->string();

            $user = $this->factoryUsers->users->getByUid($user_id, true);

            if (!$user->access_token) {
                $user->access_token = $access_token;
            }

            if ($expires_in > 0) {
                $user->access_token_expire = time() + $expires_in;
            } else {
                $user->access_token_expire = null;
            }

            $this->factoryUsers->users->save($user);
        } else {
            // при получении токена методом Implicit Flow
            // данные приходят в url после # заменяем на ? для того чтобы отправить данные на сервер в get запросе
            return $this->_response->setBody("<script>window.location.href = window.location.href.replace(/#/, '?');</script>");
        }

        if ($this->_request->get['state']->string('') == 'close') {
            return $this->_response->setBody('<script>window.close();</script>');
        }

        //в state передаем адрес куда переадресовать пользователя
        if ($this->_request->get['state']->string('') != '') {
            return $this->_response->setLocation($this->_request->get['state']->string());
        }

        //перенаправление пользователя по умолчанию
        return $this->_response->setLocation('/users/general');
    }

    public function actionPost()
    {
        return null;
    }
}
