<?php

namespace Service\Grabber;

class Controller_State_Client_Default extends Controller_State_Client
{
    /** @var Model_grabbers_grabber */
    protected $_grabber = null;
    protected $_group = null;

    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setLocation('/users/login');
        }

        $this->_application->Title->addScript('/js/jquery/jquery.dd.min.js');
        $this->_application->Title->addStyle('/css/jquery/dd.min.css');

        $this->_grabber = $this->factoryGrabber->grabbers->getByUserIdIsActive($this->_application->User->userId, true);
        $isFree = $this->_request->post['isFree']->bool(false);

        if ($this->_grabber === null && !$isFree) {
            $settings = json_decode(file_get_contents(\Service\Orders\Model_Config::$settings), true);

            $vars = [
                'userId' => $this->_application->UserID,
                'isFree' => !($this->_application->User->isFree & 8),
                'settings' => $settings,
            ];

            return $this->_response->setBody(\STPL::Fetch('client/start', $vars));
        }

        if ($this->_request->get['toggle']->int(0) > 0) {
            $groupId = $this->_request->get['toggle']->int(0);
            $group = $this->factoryGrabber->groups->getById($groupId, true);

            if ($group !== null) {
                if ($group->userId == $this->_application->User->userId) {
                    $group->userActive = !$group->userActive;
                    $this->factoryGrabber->groups->save($group);
                }
            }

            return $this->_response->setLocation($this->_request->server['REDIRECT_URL']->string());
        }

        $this->_application->Title->addScript('/js/tasks/edit.min.js');
        $this->_application->Title->addStyles(['/css/material-switch.min.css']);

        return null;
    }

    public function actionGet()
    {
        $groups = $this->factoryGrabber->groups->getByUserId($this->_grabber->userId);

        /* @var \Service\Users\Model_Notifications_Notification $notification */
        if (isset($this->_application->notifications['grabber'])) {
            foreach ($this->_application->notifications['grabber'] as $notification) {
                $notification->makeShadow();
                $notification->status = 1;
                $this->factoryUsers->notifications->save($notification);
            }
        }

        $vars = [
            'grabber' => $this->_grabber,
            'groups' => $groups,
            'user' => $this->_application->User,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/default', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'getGroups':
                return $this->_getGroups();
            case 'access_token':
                return $this->_access_token();
            case 'setGroup':
                return $this->_setGroup();
            case 'getGroupForm':
                return $this->_getGroupForm();
        }

        return $this->_response->setStatus(\System\HttpResponse::S4_METHOD_NOT_ALLOWED);
    }

    protected function _getGroups()
    {
        if ($this->_application->User->token_require) {
            return $this->_response->setJson([
                'success' => true,
                'token' => true,
                'html' => \STPL::Fetch('client/token', ['token_require' => $this->_application->User->token_require]),
            ]);
        }

        if (!$this->_application->User->access_token || ($this->_application->User->access_token_expire != null && $this->_application->User->access_token_expire < time())) {
            return $this->_response->setJson([
                'success' => true,
                'token' => true,
                'html' => \STPL::Fetch('client/token'),
            ]);
        }
        $isFree = $this->_request->post['isFree']->bool(false);

        if (!$isFree) {
            $slots = $this->_grabber->getSlots();

            if (!count($slots)) {
                $vars = [
                    'userId' => $this->_application->UserID,
                    'settings' => json_decode(file_get_contents(\Service\Orders\Model_Config::$settings), true),
                ];

                return $this->_response->setJson([
                    'success' => true,
                    'html' => \STPL::Fetch('client/form_group_buy', $vars),
                ]);
            }
        }

        $groups = $this->VK->getGroups($this->_application->User->uid, $this->_application->User->access_token);


        if (!isset($groups['items'])) {
            return $this->_response->setJson([
                'success' => true,
                'token' => true,
                'html' => \STPL::Fetch('client/token'),
            ]);
        }

        foreach ($groups['items'] as $group) {
            $_SESSION['groups'][$group['id']] = $group;
        }

        $vars = [
            'months' => $this->_request->post['months']->int(0),
            'groups' => $groups,
            'group' => $this->_group,
        ];

        if ($isFree) {
            $vars['isFree'] = true;
        }

        return $this->_response->setJson(['success' => true, 'html' => \STPL::Fetch('client/groups', $vars)]);
    }

    protected function _access_token()
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
        $this->_application->User->token_require = false;
        $this->factoryUsers->users->save($this->_application->User);

        return $this->_getGroups();
    }

    protected function _setGroup()
    {
        $isFree = $this->_request->post['isFree']->bool(false);

        if ($isFree && $this->_application->User->isFree & 8) {
            return $this->_response->setStatus(\System\HttpResponse::S4_FORBIDDEN)->setJson([
                'success' => false,
                'errorText' => 'Вы уже использовали бесплатный период',
            ]);
        }

        if ($isFree && !$this->_grabber) {
            $this->_grabber = $this->factoryGrabber->grabbers->getNew();
            $this->_grabber->userId = $this->_application->UserID;
            $this->_grabber->dateCreate = time();
            $this->_grabber->isActive = true;
            $this->_grabber->dateValid += strtotime('+1 MONTH');
            $slots = [];
            $this->_grabber->setSlots($slots);
            $this->factoryGrabber->grabbers->save($this->_grabber);
        }

        $months = $this->_request->post['months']->int(0);

        $this->_grabber->makeShadow();

        if (!$isFree) {
            $slots = $this->_grabber->getSlots();

            if ($months > 0) {
                foreach ($slots as $id => $slot) {
                    if ($slot == $months) {
                        unset($slots[$id]);
                        break;
                    }
                }
            } else {
                $slot = array_shift($slots);
            }

            if (!$slot) {
                $vars = [
                    'settings' => json_decode(file_get_contents(\Service\Orders\Model_Config::$settings), true),
                ];

                return $this->_response->setJson([
                    'success' => true,
                    'html' => \STPL::Fetch('client/form_group_buy', $vars),
                ]);
            }
            $this->_grabber->setSlots($slots);
        } else {
            $slot = 1;
        }

        $groupId = $this->_request->post['groupId']->int();

        if (!isset($_SESSION['groups'][$groupId])) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => 'Необходимо указать группу, для которой вы являетесь владельцем',
            ]);
        }

        $item = $_SESSION['groups'][$groupId];

        $query = $this->factoryGrabber->groups->query();
        $query->filter->fieldValue('ownerId', '=', strval($item['id']));
        $it = $query->iteratorForSave();
        $group = $it->current();

        if ($group) {
            if ($group->wasFree && $isFree) {
                return $this->_response->setJson([
                    'success' => false,
                    'errorText' => 'Данная группа участвовала в пробном режиме',
                ]);
            }

            if ($group->userId != $this->_application->UserID && !$this->_request->post['take']->bool(false)) {
                $user = $this->factoryUsers->users->getById($group->userId);

                return $this->_response->setJson([
                    'success' => false,
                    'errorText' => '<h5 class="text-center">Данная группа активирована пользователем <strong>' . $user->name . '</strong>.</h5><div class="text-center"><button onclick="grabber.saveClick(true);" class="btn btn-success">Перенести группу</button></div>',
                ]);
            }

            if ($group->userId != $this->_application->UserID) {
                $this->factoryGrabber->groups->delete($group);
                $group = $this->factoryGrabber->groups->getNew();
                $group->title = $item['name'];
                $group->photo = $item['photo_50'];
                $group->ownerId = strval($item['id']);
                $group->grabberId = $this->factoryGrabber->grabbers->getByUserIdIsActive($this->_application->UserID,
                    true)->grabberId;
                $group->userId = $this->_application->UserID;
                $group->dateValid = strtotime('+' . $slot . ' MONTH');
                $group->isActive = true;
                $group->url = 'https://vk.com/' . $item['screen_name'];
                $group->interval = 30;
            }

            if ($group->isFree && !$isFree) {
                $group->isFree = false;
                $group->isFreeCount = 0;
                $group->dateValid = strtotime('+' . $slot . ' MONTH', time());
            } else {
                $group->dateValid = strtotime('+' . $slot . ' MONTH', max($group->dateValid, time()));
            }

            if ($this->factoryGrabber->groups->save($group)) {
                $query = $this->factoryUsers->notifications->query();
                $query->filter->fieldValue('userId', '=', $this->_application->UserID);
                $query->filter->fieldValue('service', '=', 'grabber');
                $query->filter->fieldValue('objectId', '=', $group->groupId);
                $it = $query->iteratorForSave();

                foreach ($it as $notification) {
                    $notification->status = 2;
                    $this->factoryUsers->notifications->save($notification);
                }
                $this->factoryGrabber->grabbers->save($this->_grabber);
            }

            return $this->_response->setJson(['success' => true, 'reload' => true]);
        }

        $group = $this->factoryGrabber->groups->getNew();
        $group->title = $item['name'];
        $group->photo = $item['photo_50'];
        $group->ownerId = strval($item['id']);
        $group->grabberId = $this->factoryGrabber->grabbers->getByUserIdIsActive($this->_application->UserID,
            true)->grabberId;
        $group->userId = $this->_application->UserID;
        $group->dateValid = strtotime('+' . $slot . ' MONTH');
        $group->isActive = true;
        $group->url = 'https://vk.com/' . $item['screen_name'];
        $group->interval = 30;

        if ($isFree) {
            $config = \Service\Orders\Model_Config::getSettings();
            $group->isFree = true;
            $group->wasFree = true;
            $group->isFreeCount = intval($config['grabber']['free']);
        }

        if ($this->factoryGrabber->groups->save($group)) {
            $this->factoryGrabber->grabbers->save($this->_grabber);

            if ($isFree) {
                $this->_application->User->makeShadow();
                $this->_application->User->isFree = $this->_application->User->isFree | 8;
                $this->factoryUsers->users->save($this->_application->User);
            }

            return $this->_response->setJson(['success' => true, 'reload' => true]);
        }

        return $this->_response->setJson(['success' => false, 'errorText' => 'Ошибка сервера']);
    }
}
