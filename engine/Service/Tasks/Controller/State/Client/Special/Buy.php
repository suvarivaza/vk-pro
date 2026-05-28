<?php

namespace Service\Tasks;


class Controller_State_Client_Special_Buy extends Controller_State_Client_Special
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

        if (!$this->_application->User->access_token || ($this->_application->User->access_token_expire != null && $this->_application->User->access_token_expire < time())) {
            return $this->_response->setBody(\STPL::Fetch('client/token', ['app' => $this->_application]));
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
                return $this->_response->setLocation('/tasks/special');
            }

            $specials = $this->factoryTasks->specialGroups->getByUserId($this->_application->UserID);

            foreach ($specials as $special) {
                if ($special->ownerId == $groupId) {
                    $special->makeShadow();
                    $special->photo = $found['photo_50'];
                    $this->factoryTasks->specialGroups->save($special);

                    return $this->_response->setLocation('/tasks/special/' . $special->specialId . '/all/1');
                }
            }

            $special = $this->factoryTasks->specialGroups->getNew();
            $special->userId = $this->_application->UserID;
            $special->url = 'https://vk.com/' . $found['screen_name'];
            $special->ownerId = strval($found['id']);
            $special->title = $found['name'];
            $special->photo = $found['photo_50'];
            $this->factoryTasks->specialGroups->save($special);

            return $this->_response->setLocation('/tasks/special/' . $special->specialId . '/all/1');
        }


        $groups = $this->VK->getGroups($this->_application->User->uid, $this->_application->User->access_token);

        if (!isset($groups['items'])) {
            return $this->_response->setBody(\STPL::Fetch('client/token', ['app' => $this->_application]));
        }

        $vars = [
            'groups' => $groups,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/special/buy', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'access_token':
                return $this->_access_token();
        }

        return $this->_response->setStatus(\System\HttpResponse::S4_METHOD_NOT_ALLOWED);
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
