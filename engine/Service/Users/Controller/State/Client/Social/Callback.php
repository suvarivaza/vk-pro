<?php

namespace Service\Users;

class Controller_State_Client_Social_Callback extends Controller_State_Client
{
    public function actionGet()
    {

        // Вернет код. АПИ - https://vk.com/dev/authcode_flow_user
        //Пример запроса: https://oauth.vk.com/authorize?client_id=7796644&redirect_uri=http://vk-pro.top/users/social/callback&scope=offline,video,photos,wall,groups,stats&response_type=code&state=/&v=5.64
        $code = $this->_request->get['code']->string('');
        $response = file_get_contents('https://oauth.vk.com/access_token?client_id=' . VK_ID . '&client_secret=' . $this->_application->settings['secret'] . '&redirect_uri=' . urlencode(VK_REDIRECT_URL) . '&code=' . $code);
        $json = json_decode($response, true);

//        var_dump($response); die;

        if (isset($json['access_token'])) {
            $user = $this->factoryUsers->users->getByUid($json['user_id'], true);

            if (!$user->access_token) {
                $user->access_token = $json['access_token'];
            }

            if ($json['expires_in'] > 0) {
                $user->access_token_expire = time() + $json['expires_in'];
            } else {
                $user->access_token_expire = null;
            }

            $this->factoryUsers->users->save($user);
        }

        if ($this->_request->get['state']->string('') == 'close') {
            return $this->_response->setBody('<script>window.close();</script>');
        }

        if ($this->_request->get['state']->string('') != '') {
            return $this->_response->setLocation($this->_request->get['state']->string());
        }

        return $this->_response->setLocation('/tasks/my/all/1');
    }

    public function actionPost()
    {
        return null;
    }
}
