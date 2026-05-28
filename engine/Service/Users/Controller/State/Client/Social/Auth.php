<?php

//INFO: Авторизация через VK OAuth (используется прямой редирект на VK)

namespace Service\Users;

class Controller_State_Client_Social_Auth extends Controller_State_Client
{
    public function actionGet()
    {
        // Перенаправляем на VK OAuth
        return $this->_response->setLocation('https://oauth.vk.com/authorize?client_id=' . VK_ID . '&redirect_uri=' . urlencode(VK_REDIRECT_URL) . '&scope=offline,video,photos,wall,groups,stats&response_type=code&state=/&v=5.64');
    }

    public function actionPost()
    {
        // POST больше не используется, т.к. ulogin.ru отключен
        return $this->_response->setLocation('/');
    }
}
