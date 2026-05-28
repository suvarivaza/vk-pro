<?php

namespace Service\Auto;

use STPL;
use System\HttpResponse;

class Controller_State_Client_Buy extends Controller_State_Client
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        $this->_application->Title->addScript('/js/jquery/jquery.dd.min.js');
        $this->_application->Title->addStyle('/css/jquery/dd.min.css');

        return null;
    }

    public function actionGet()
    {
        $auto = $this->factoryAuto->auto->getByUserIdIsActive($this->_application->UserID, true);

        if ($auto === null) {
            $auto = $this->factoryAuto->auto->getNew();
            $auto->userId = $this->_application->UserID;
            $auto->dateCreate = time();
            $auto->dateValid = strtotime('+1 MONTH');
            $auto->isActive = true;
            $this->factoryAuto->auto->save($auto);
        }

        return $this->_response->setLocation('/auto');
    }

    public function actionGetOld()
    {
        if (!$this->_application->User->access_token || ($this->_application->User->access_token_expire != null && $this->_application->User->access_token_expire < time())) {
            return $this->_response->setBody(STPL::Fetch('client/token', ['app' => $this->_application]));
        }

        $groupId = $this->_request->get['groupId']->int(0);

        if ($groupId) {

            $groups = $this->VK->getGroups($this->_application->User->uid, $this->_application->User->access_token);

            $found = false;
            foreach ($groups['items'] as $group) {
                if ($group['id'] == $groupId) {
                    $found = $group;
                    break;
                }
            }

            if (!$found) {
                return $this->_response->setLocation('/auto');
            }

            $autos = $this->factoryAuto->auto->getByUserId($this->_application->UserID);

            foreach ($autos as $auto) {
                if ($auto->ownerId == $groupId) {
                    $auto->makeShadow();
                    $auto->photo = $found['photo_50'];
                    $this->factoryAuto->auto->save($auto);

                    return $this->_response->setLocation('/auto/list/' . $auto->autoId);
                }
            }

            $auto = $this->factoryAuto->auto->getNew();
            $auto->userId = $this->_application->UserID;
            $auto->url = 'https://vk.com/' . $found['screen_name'];
            $auto->ownerId = strval($found['id']);
            $auto->title = $found['name'];
            $auto->photo = $found['photo_50'];
            $this->factoryAuto->auto->save($auto);

            return $this->_response->setLocation('/auto/list/' . $auto->autoId);
        }


        $groups = $this->VK->getGroups($this->_application->User->uid, $this->_application->User->access_token);

        if (empty($groups['items'])) {
            return $this->_response->setBody(STPL::Fetch('client/token', ['app' => $this->_application]));
        }

        $vars = [
            'groups' => $groups,
        ];

        return $this->_response->setBody(STPL::Fetch('client/buy', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'access_token':
                return $this->_access_token();
        }

        return $this->_response->setStatus(HttpResponse::S4_METHOD_NOT_ALLOWED);
    }

    public function _access_token()
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

        return null;
    }
}
