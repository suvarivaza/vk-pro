<?php

namespace Service\Users;

class Controller_State_Client_Register extends Controller_State_Client
{
    public function actionGet()
    {
        $this->_application->page = '';
        $this->_application->Title->addScript('/js/register.min.js');

        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setLocation('/');
        }

        return $this->_response->setBody(\STPL::Fetch('client/register', ['user' => $this->_application->User]));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'register':
                return $this->_register();
        }

        return $this->_response->setStatus(\System\HttpResponse::S4_BAD_REQUEST);
    }

    protected function _register()
    {
        $login = $this->_request->post['login']->string();
        $email = $this->_request->post['email']->email();
        $password = $this->_request->post['password']->string();
        $passwordConfirm = $this->_request->post['passwordConfirm']->string();

        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => '<div class="alert alert-danger">Сначала войдите через ВКонтакте</div>',
            ]);
        }

        if (!$login) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => '<div class="alert alert-danger">Укажите Ваш логин</div>',
            ]);
        }

        if (!$email) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => '<div class="alert alert-danger">Укажите Ваш е-майл для регистрации</div>',
            ]);
        }

        if (!$password) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => '<div class="alert alert-danger">Укажите Ваш пароль для регистрации</div>',
            ]);
        }

        if ($password != $passwordConfirm) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => '<div class="alert alert-danger">Пароль и подтверждение пароля не совпадают</div>',
            ]);
        }

        $user = $this->factoryUsers->users->getByLogin($login);

        if ($user !== null && $user->userId != $this->_application->User->userId) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => '<div class="alert alert-danger">Такой логин занят. Используйте другой.</div>',
            ]);
        }

        $user = $this->factoryUsers->users->getByEmail($email);

        if ($user !== null && $user->userId != $this->_application->User->userId) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => '<div class="alert alert-danger">Регистрация с данным е-майл уже есть. Воспользуйтесь авторизацией через ВКонтакте.</div>',
            ]);
        }

        $user = $this->_application->User;
        $user->makeShadow();
        $user->login = $login;
        $user->password = md5($this->_request->post['password']->string(''));
        $user->ceed = \Lib_Uuid::getNext();
        $user->token = md5($user->login . $user->password . $user->ceed);
        $user->dateUpdate = time();
        $user->confirmed = 0;
        $user->userType = 0;
        $user->email = $email;

        if ($this->factoryUsers->users->save($user)) {
            $this->_application->setUserToCookie($this->_application->User);

            $mail = new \PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.mail.ru';
            $mail->SMTPAuth = true;
            $mail->Username = 'info@vk-pro.top';
            $mail->Password = '9wQu%T8OXdfe';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            $mail->setFrom('info@vk-pro.top');
            $mail->addAddress($user->email);
            $mail->Subject = '[VK-PRO.TOP] Регистрация на сайте';

            $mail->addCustomHeader('Return-Path', 'info@vk-pro.top');
            $mail->addCustomHeader('X-Confirm-Reading-To', 'info@vk-pro.top');
            $mail->addCustomHeader('Disposition-Notification-To', 'info@vk-pro.top');
            $mail->CharSet = 'UTF-8';
            $html = \STPL::Fetch('mail/register',
                ['user' => $user, 'password' => $this->_request->post['password']->string('')]);

            $mail->msgHTML($html);

            $mail->smtpConnect([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ]);

            if (!$mail->send()) {
                error_log($mail->isError());
            }

            return $this->_response->setJson([
                'success' => true,
                'errorText' => '<div class="alert alert-success">Спасибо за регистрацию. На указаный адрес отправлено письмо для подтверждения. Активируйте учетную запись перейдя по ссылке в письме</div>',
            ]);
        }

        return $this->_response->setJson([
            'success' => false,
            'errorText' => '<div class="alert alert-danger">Не удалось сздать пользователя. Попробуйте позднее</div>',
        ]);
    }
}
