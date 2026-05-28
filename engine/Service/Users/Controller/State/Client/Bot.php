<?php

namespace Service\Users;

class Controller_State_Client_Bot extends Controller_State_Client
{
    public function actionPrepare()
    {
        $this->_application->Title->addStyles(['/css/material-switch.min.css']);

        $this->_application->userPage = 'bot';
        $this->_application->Title->Title = 'Настройки бота';

        return parent::actionPrepare();
    }

    public function actionGet()
    {
        //выключение автобота у пользователя
        if ($this->_request->get['isBotDisable']->int(0) == 1) {
            $this->_application->User->makeShadow();
            $this->_application->User->isBot = 0;
            $this->factoryUsers->users->save($this->_application->User);

            return $this->_response->setLocation($this->_request->server['REDIRECT_URL']->string());
        }

        //включение автобота у пользователя
        if ($this->_request->get['isBot']->int(0) == 1) {
            $this->_application->User->makeShadow();
            $this->_application->User->isBot = array_sum(array_keys(\Service\Tasks\Model_Config::$botTypes)); //разобраться почему пишем именно это значение и почему там половина закомментирована
            $this->factoryUsers->users->save($this->_application->User);
            return $this->_response->setLocation($this->_request->server['REDIRECT_URL']->string());
        }

        $vars = [
            'user' => $this->_application->User,
            'botTypes' => \Service\Tasks\Model_Config::$botTypes,
            'types' => \Service\Tasks\Model_Config::$types,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/bot', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string('');

        switch ($action) {
            case 'botSettingsSave':
                return $this->_botSettingsSave();
        }

        return $this->_response->setStatus(\System\HttpResponse::S4_BAD_REQUEST);
    }

    private function _botSettingsSave()
    {
        return null;
        $this->_application->User->makeShadow();
        $this->_application->User->isBot = array_sum($this->_request->post['botType']->asArray([],
            \System\HttpRequest::INTEGER_NUM));
        $this->factoryUsers->users->save($this->_application->User);

        return null;
    }
}
