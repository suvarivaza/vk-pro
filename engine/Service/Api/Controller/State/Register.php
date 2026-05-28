<?php

namespace Service\Api;

use Lib_Uuid;
use System\HttpResponse;
use System\Service_Controller_State;

class Controller_State_Client_Register extends Service_Controller_State
{
    public function actionGet()
    {
        return $this->_response->setStatus(HttpResponse::S4_BAD_REQUEST);
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            default:
                return $this->_register();
        }
    }

    private function _register()
    {
        $login = $this->_request->post['login']->string();
        $email = $this->_request->post['email']->email();
        $password = $this->_request->post['password']->string();
        $passwordConfirm = $this->_request->post['passwordConfirm']->string();

        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Сначала войдите через ВКонтакте']);
        }

        if (!$login) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Укажите Ваш логин']);
        }

        if (!$email) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Укажите Ваш е-майл для регистрации']);
        }

        if (!$password) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Укажите Ваш пароль для регистрации']);
        }

        if ($password != $passwordConfirm) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => 'Пароль и подтверждение пароля не совпадают',
            ]);
        }

        $user = $this->factoryUsers->users->getByLogin($login);

        if ($user !== null && $user->userId != $this->_application->User->userId) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => 'Такой логин занят. Используйте другой.',
            ]);
        }

        $user = $this->factoryUsers->users->getByEmail($email);

        if ($user !== null && $user->userId != $this->_application->User->userId) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => 'Регистрация с данным е-майл уже есть. Воспользуйтесь авторизацией через ВКонтакте.',
            ]);
        }

        $user = $this->_application->User;
        $user->makeShadow();
        $user->login = $login;
        $user->password = md5($this->_request->post['password']->string(''));
        $user->ceed = Lib_Uuid::getNext();
        $user->token = md5($user->login . $user->password . $user->ceed);
        $user->dateUpdate = time();
        $user->confirmed = 0;
        $user->userType = 0;
        $user->email = $email;

        if ($this->factoryUsers->users->save($user)) {
            return $this->_response->setJson(['success' => true, 'errorText' => 'Спасибо за регистрацию.']);
        }

        return $this->_response->setJson([
            'success' => false,
            'errorText' => 'Не удалось сздать пользователя. Попробуйте позднее',
        ]);
    }
}
