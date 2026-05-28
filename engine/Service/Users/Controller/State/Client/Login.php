<?php

namespace Service\Users;

class Controller_State_Client_Login extends Controller_State_Client
{
    public function actionGet()
    {

        $this->_application->page = '';

        if ($this->_application->UserIsAuth()) {
            return $this->_response->setLocation('/tasks/all');
        }

        if ($this->_request->get['referrerUrl']->string('')) {
            $this->_response->setCookie('referrerUrl', $this->_request->get['referrerUrl']->string(''));

            return $this->_response->setLocation('/users/login');
        }

        return $this->_response->setBody(\STPL::Fetch('client/login'));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'login':
                return $this->_login();
            case 'register':
                return $this->_register();
        }

        return $this->_response->setStatus(\System\HttpResponse::S4_BAD_REQUEST);
    }

    protected function _login()
    {
        $email = $this->_request->post['email']->string();
        $password = $this->_request->post['password']->string();

        $user = $this->factoryUsers->users->getUserByLoginPass($email, md5($password));

        if ($user === null) {
            $user = $this->factoryUsers->users->getUserByEmailPass($email, md5($password));

            if ($user === null) {
                return $this->_response->setJson([
                    'success' => false,
                    'errorText' => '<div class="alert alert-danger">Пользователь с таким е-майл и паролем не найден. Проверьте правильность ввода пароля.</div>',
                ]);
            }
        }

        $this->_application->setUserToCookie($user);

        return $this->_response->setJson(['success' => true]);
    }

    protected function _register()
    {
        $email = $this->_request->post['email']->string();
        $password = $this->_request->post['password']->string();
        $firstName = $this->_request->post['firstName']->string();

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

        if (!$firstName) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => '<div class="alert alert-danger">Укажите Ваше имя для регистрации</div>',
            ]);
        }

        $user = $this->factoryUsers->users->getByLogin($email);

        if ($user !== null) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => '<div class="alert alert-danger">Вы уже зарегистрированы. Воспользуйтесь формой авторизации</div>',
            ]);
        }

        $user = $this->factoryUsers->users->getNewUser();
        $user->login = $email;
        $user->password = md5($this->_request->post['password']->string(''));
        $user->ceed = \Lib_Uuid::getNext();
        $user->token = md5($user->login . $user->password . $user->ceed);
        $user->firstName = $firstName;
        $user->name = $firstName;
        $user->dateCreate = time();
        $user->dateUpdate = time();
        $user->confirmed = 0;
        $user->userType = 0;
        $user->email = $user->login;

        if ($this->factoryUsers->users->save($user)) {
            $this->_application->Log->Log(\Service\Logs\Model_Config::USER_CREATE, $user->userId, $user->userId,
                ['login' => $user->login, 'email' => $user->email]);

            $mail = new \PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.mail.ru';
            $mail->SMTPAuth = true;
            $mail->Username = 'info@vk-pro.top';
            $mail->Password = '040176i';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            $mail->setFrom('info@vk-pro.top');
            $mail->addAddress($user->login);
            $mail->Subject = 'Регистрация на сайте';

            $mail->addCustomHeader('Return-Path', 'info@vk-pro.top');
            $mail->addCustomHeader('X-Confirm-Reading-To', 'info@vk-pro.top');
            $mail->addCustomHeader('Disposition-Notification-To', 'info@vk-pro.top');
            $mail->CharSet = 'UTF-8';
            $mail->msgHTML('Добрый день! Вы зарегистрировались на сайте. Для подтверждения почтового ящика перейдите по ссылке: <a href="https://vk-pro.top/users/confirm?token=' . urlencode($user->token) . '">Перейти</a>');

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
            unset($mail);
        }

        return $this->_response->setJson([
            'success' => false,
            'errorText' => '<div class="alert alert-danger">Не удалось сздать пользователя. Попробуйте позднее</div>',
        ]);
    }
}
