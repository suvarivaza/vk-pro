<?php

namespace Service\Users;

class Controller_State_Client_General extends Controller_State_Client
{
    private $_success = null;
    private $prefix = [
        7 => ['code' => 'RU', 'title' => 'Россия (+7)'],
        77 => ['code' => 'KZ', 'title' => 'Казахстан (+77)'],
        380 => ['code' => 'UA', 'title' => 'Украина (+380)'],
        371 => ['code' => 'LV', 'title' => 'Латвия (+371)'],
        370 => ['code' => 'LT', 'title' => 'Литва (+370)'],
        996 => ['code' => 'KG', 'title' => 'Кыргызстан (+996)'],
        9955 => ['code' => 'GE', 'title' => 'Грузия (+9955)'],
        992 => ['code' => 'TJ', 'title' => 'Таджикистан (+992)'],
        373 => ['code' => 'MD', 'title' => 'Молдавия (+373)'],
        84 => ['code' => 'VN', 'title' => 'Вьетнам (+84)'],
        91 => ['code' => 'IN', 'title' => 'Индия (+91)'],
        994 => ['code' => 'AZ', 'title' => 'Азербайджан (+994)'],
        82 => ['code' => 'KR', 'title' => 'Южная Корея (+82)'],
        372 => ['code' => 'EE', 'title' => 'Эстония (+372)'],
        375 => ['code' => 'BY', 'title' => 'Беларусь (+375)'],
        374 => ['code' => 'AM', 'title' => 'Армения (+374)'],
        44 => ['code' => 'GB', 'title' => 'Великобритания (+44)'],
        998 => ['code' => 'UZ', 'title' => 'Узбекистан (+998)'],
        972 => ['code' => 'IL', 'title' => 'Израиль (+972)'],
        66 => ['code' => 'TH', 'title' => 'Таиланд (+66)'],
        90 => ['code' => 'TR', 'title' => 'Турция (+90)'],
        81 => ['code' => 'JP', 'title' => 'Япония (+81)'],
        1 => ['code' => 'US', 'title' => 'США (+1)'],
        507 => ['code' => 'PA', 'title' => 'Панама (+507)'],
    ];

    public function actionPrepare()
    {
        $this->_application->userPage = 'general';
        $this->_application->Title->Title = 'Профиль пользователя';
        $this->_application->Title->add('link', [
            'rel' => 'icon',
            'href' => '/img/icons/32/icon-profile.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->add('link', [
            'rel' => 'shortcut icon',
            'href' => '/img/icons/32/icon-profile.png',
            'type' => 'image/png',
        ]);

        $reponse = parent::actionPrepare();

        if ($reponse !== null) {
            return $reponse;
        }

        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setLocation('/users/login');
        }

        return null;
    }

    public function actionGet()
    {
        $vars = [
            'action' => $this->_request->post['action']->string(''),
            'errors' => $this->_errors,
            'success' => $this->_success,
            'prefix' => $this->prefix,
            'user' => $this->_application->User,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/general', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'qiwi':
                return $this->_qiwi();
            case 'user':
                return $this->_userSave();
            case 'password':
                return $this->_password();
            case 'age_limits':
                return $this->_age_limits();
        }

        return $this->_response->setStatus(\System\HttpResponse::S4_BAD_REQUEST);
    }

    private function _qiwi()
    {
        $user = $this->_application->User;
        $user->makeShadow();
        $user->qiwi_prefix = $this->_request->post['qiwi_prefix']->string();
        $user->qiwi = $this->_request->post['qiwi']->string();
        $this->factoryUsers->users->save($user);

        return null;
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

    private function _password()
    {
        $passwordOld = $this->_request->post['passwordOld']->string('');
        $passwordNew = $this->_request->post['passwordNew']->string('');
        $passwordConfirm = $this->_request->post['passwordConfirm']->string('');

        if ($passwordNew != $passwordConfirm) {
            $this->_errors[] = 'Пароль и подтверждение должны совпадать';
        }

        if (md5($passwordOld) != $this->_application->User->password) {
            $this->_errors[] = 'Текущий пароль указан не верно';
        }

        if (count($this->_errors)) {
            return false;
        }

        $this->_application->User->makeShadow();
        $this->_application->User->password = md5($passwordNew);
        $this->_application->User->ceed = \Lib_Uuid::getNext();
        $this->_application->User->token = md5($this->_application->User->login . $this->_application->User->password . $this->_application->User->ceed);

        if ($this->factoryUsers->users->save($this->_application->User)) {
            $this->_application->setUserToCookie($this->_application->User);
            $this->_success = true;
        }

        return false;
    }

    private function _age_limits()
    {
        $user = $this->_application->User;
        $user->makeShadow();
        $user->age_limits = $this->_request->post['age_limits']->int(0);
        $this->factoryUsers->users->save($user);

        return $this->_response->setLocation($this->_request->server['REDIRECT_URL']->string());
    }
}
