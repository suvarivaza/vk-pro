<?php

namespace Service\System;

class Controller_Client_Default extends Controller_Client
{
    private $_template = 'states/default';

    public function actionPrepare()
    {
        $this->_application->menu['main']['active'] = true;

        if ($this->_request->get['referrerUrl']->string('')) {
            $this->_response->setCookie('referrerUrl', $this->_request->get['referrerUrl']->string(''));

            return $this->_response->setLocation('/');
        }

        parent::actionPrepare();
    }

    /**
     * Обработчик GET-запросов
     *
     * @return mixed
     */
    public function actionGet()
    {
        $this->_application->Title->add('meta', [
            'name' => 'og:title',
            'content' => $this->_application->Title->Title,
        ]);
        $this->_application->Title->add('meta', [
            'name' => 'og:description',
            'content' => $this->_application->Title->Description,
        ]);
        $this->_application->Title->add('meta', [
            'name' => 'og:type',
            'content' => 'website',
        ]);

        $this->_application->Title->add('meta', [
            'name' => 'og:image',
            'content' => 'https://vk-pro.top/img/logo.white.135.png',
        ]);
        $this->_application->Title->add('meta', [
            'name' => 'og:image:type',
            'content' => 'image/png',
        ]);
        $this->_application->Title->add('meta', [
            'name' => 'og:image:width',
            'content' => '616',
        ]);
        $this->_application->Title->add('meta', [
            'name' => 'og:image:height',
            'content' => '179',
        ]);

        $vars = [
            'app' => $this->_application,
        ];

        if ($this->_request->cookie['userToken']->string('') == '') {
            \Lib_Uuid::getNext();
        }

        if ($this->_application->UserIsAuth() && $this->_application->User->dateUpdate < time() - 86400) {
            $this->_application->User->makeShadow();
            $this->_application->User->dateUpdate = time();
            $this->factoryUsers->users->save($this->_application->User);

            return $this->_response->setLocation('https://oauth.vk.com/authorize?client_id=' . VK_ID . '&redirect_uri=' . VK_REDIRECT_URL . '&scope=offline,video,photos,wall,groups,stats&response_type=code&state=/&v=5.64');
        }

        if ($this->_application->UserIsAuth()) {
            return $this->_response->setLocation('/tasks/all');
        }

        return $this->_response->setBody(
            \STPL::Fetch(
                $this->_template, $vars
            )
        );
    }

    public function actionPost()
    {
        $token = $this->_request->post['token']->string('');

        if ($token) {
            $s = file_get_contents('http://ulogin.ru/token.php?token=' . $token .
                '&host=' . $this->_request->server['HTTP_HOST']->string(''));
            $user = json_decode($s, true);

            if ($user['error']) {
                $this->_response->setCookie('token', '', strtotime('-1 YEAR'));
            }
            $this->_response->setCookie('token', $token, strtotime('+1 YEAR'));

            return null;
        }

        return parent::actionPost();
    }
}
