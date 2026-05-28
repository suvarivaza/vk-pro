<?php

namespace Service\Users;

class Controller_State_Client_Remind extends Controller_State_Client
{
    /**
     * @return \System\HttpResponse|null
     */
    public function actionGet()
    {
        if (isset($this->_request->get['token'])) {
            $user = $this->factory->users->getByToken($this->_request->get['token']->string());

            if ($user === null) {
                return $this->_response->setStatus(\System\HttpResponse::S3_FOUND)->setHeader('Location',
                    '/users/login');
            }

            return $this->_response->setBody(\STPL::Fetch('client/repassword', ['errors' => $this->_errors]));
        }

        return $this->_response->setBody(\STPL::Fetch('client/remind', ['errors' => $this->_errors]));
    }

    /**
     * @return \System\HttpResponse|null
     */
    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'send':
                return $this->_send();
            case 'new':
                return $this->_new();
        }
    }

    private function _send()
    {
        $email = $this->_request->post['email']->email();

        if (!$email) {
            $this->_errors[] = 'Неверно указан email';

            return null;
        }

        $query = $this->factory->users->query();
        $query->limit(1)->sqlCalcFoundRows(true);
        $query->filter->fieldValue('email', 'LIKE', $email);

        $it = $query->iterator();

        if (!$it->getTotal()) {
            $this->_errors[] = 'Неверно указан email';

            return null;
        }

        /** @var Model_Users_User $user */
        $user = $it->current();
        $link = 'http://' . DOMAIN . '/users/remind?token=' . $user->token;

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
        $mail->Subject = '[VK-PRO.TOP] Восстановление пароля';

        $mail->addCustomHeader('Return-Path', 'info@vk-pro.top');
        $mail->addCustomHeader('X-Confirm-Reading-To', 'info@vk-pro.top');
        $mail->addCustomHeader('Disposition-Notification-To', 'info@vk-pro.top');
        $mail->CharSet = 'UTF-8';
        $mail->msgHTML(\STPL::Fetch('mail/remind', ['confirm' => $link]));

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

        return $this->_response->setBody(\STPL::Fetch('client/remind',
            ['success' => true, 'errors' => $this->_errors]));
    }

    private function _new()
    {
        $token = $this->_request->get['token']->string();
        $user = $this->factory->users->getByToken($token);

        if ($user === null) {
            return $this->_response->setStatus(\System\HttpResponse::S3_FOUND)->setHeader('Location', '/users/login');
        }

        $user->makeShadow();

        $passwordNew = $this->_request->post['password']->string('');
        $passwordConfirm = $this->_request->post['passwordConfirm']->string('');

        if ($passwordNew != $passwordConfirm) {
            $this->_errors[] = 'Пароль и подтверждение должны совпадать';

            return null;
        }

        $user->password = md5($passwordNew);
        $user->ceed = \Lib_Uuid::getNext();
        $user->token = md5($user->login . $user->password . $user->ceed);

        if ($this->_errors !== null) {
            return null;
        }
        $this->factory->users->save($user);

        return $this->_response->setStatus(\System\HttpResponse::S3_FOUND)->setHeader('Location', '/users/login');
    }
}
