<?php

namespace Service\Tasks;

class Controller_State_Client_Special_Default extends Controller_State_Client_Special
{
    /** @var Model_Specials_Groups_Group */
    protected $_group = null;
    /** @var Model_Specials_Special */
    protected $_special = null;

    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setLocation('/users/login');
        }

        if ($this->_application->User->login == '') {
            return $this->_response->setLocation('/users/register');
        }

        $this->_application->Title->addScript('/js/jquery/jquery.dd.min.js');
        $this->_application->Title->addStyle('/css/jquery/dd.min.css');

        $this->_special = $this->factoryTasks->specials->getByUserIdIsActive($this->_application->User->userId, true);
        $isFree = $this->_request->post['isFree']->bool(false);

        $this->_application->Title->addScript('/js/special.min.js');

        if ($this->_special === null && !$isFree) {
            $settings = json_decode(file_get_contents(\Service\Orders\Model_Config::$settings), true);

            $vars = [
                'userId' => $this->_application->UserID,
                'isFree' => !($this->_application->User->isFree & 16),
                'settings' => $settings,
            ];

            return $this->_response->setBody(\STPL::Fetch('client/start', $vars));
        }

        $this->_application->Title->addScript('/js/tasks/edit.min.js');
        $this->_application->Title->addStyles(['/css/material-switch.min.css']);

        $this->_application->Title->add('link', [
            'rel' => 'icon',
            'href' => '/img/icons/32/icon-special.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->add('link', [
            'rel' => 'shortcut icon',
            'href' => '/img/icons/32/icon-special.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->Title = 'Спецзадания';

        return null;
    }

    public function actionGet()
    {
        $groups = $this->factoryTasks->specialGroups->getByUserId($this->_special->userId);

        /** @var \Service\Users\Model_Notifications_Notification $notification */
        if(isset($this->_application->notifications['special'])){
            foreach ($this->_application->notifications['special'] as $notification) {
                $notification->makeShadow();
                $notification->status = 1;
                $this->factoryUsers->notifications->save($notification);
            }
        }


        $vars = [
            'special' => $this->_special,
            'groups' => $groups,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/special/default', $vars));
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
        if (!$this->_application->User->access_token || ($this->_application->User->access_token_expire != null && $this->_application->User->access_token_expire < time())) {
            return $this->_response->setJson([
                'success' => true,
                'token' => true,
                'html' => \STPL::Fetch('client/token'),
            ]);
        }

        $isFree = $this->_request->post['isFree']->bool(false);

        if (!$isFree) {
            $slots = $this->_special->getSlots();

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
        $this->factoryUsers->users->save($this->_application->User);

        return $this->_getGroups();
    }

    protected function _setGroup()
    {
        $isFree = $this->_request->post['isFree']->bool(false);

        if ($isFree && $this->_application->User->isFree & 16) {
            return $this->_response->setStatus(\System\HttpResponse::S4_FORBIDDEN)->setJson([
                'success' => false,
                'errorText' => 'Вы уже использовали бесплатный период',
            ]);
        }

        if ($isFree && !$this->_special) {
            $this->_special = $this->factoryTasks->specials->getNew();
            $this->_special->userId = $this->_application->UserID;
            $this->_special->dateCreate = time();
            $this->_special->isActive = true;
            $this->_special->dateValid += strtotime('+1 MONTH');
            $slots = [];
            $this->_special->setSlots($slots);
            $this->factoryTasks->specials->save($this->_special);
        }

        $months = $this->_request->post['months']->int(0);

        $this->_special->makeShadow();

        if (!$isFree) {
            $slots = $this->_special->getSlots();

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
            $this->_special->setSlots($slots);
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

        $query = $this->factoryTasks->specialGroups->query();
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

            if ($group->userId != $this->_application->UserID) {
                return $this->_response->setJson([
                    'success' => false,
                    'errorText' => 'Данная группа активирована другим пользователем',
                ]);
            }

            if ($group->userId != $this->_application->UserID && !$this->_request->post['take']->bool(false)) {
                $user = $this->factoryUsers->users->getById($group->userId);

                return $this->_response->setJson([
                    'success' => false,
                    'errorText' => '<h5 class="text-center">Данная группа активирована пользователем <strong>' . $user->name . '</strong>.</h5><div class="text-center"><button onclick="special.saveClick(true);" class="btn btn-success">Перенести группу</button></div>',
                ]);
            }

            if ($group->userId != $this->_application->UserID) {
                $this->factoryTasks->specialGroups->delete($group);
                $group = $this->factoryTasks->specialGroups->getNew();
                $group->title = $item['name'];
                $group->photo = $item['photo_50'];
                $group->ownerId = strval($item['id']);
                $group->specialId = $this->factoryTasks->specials->getByUserIdIsActive($this->_application->UserID,
                    true)->specialId;
                $group->userId = $this->_application->UserID;
                $group->dateValid = strtotime('+' . $slot . ' MONTH');
                $group->url = 'https://vk.com/' . $item['screen_name'];
            }

            if ($group->isFree && !$isFree) {
                $group->isFree = false;
                $group->isFreeCount = 0;
                $group->dateValid = strtotime('+' . $slot . ' MONTH', time());
            } else {
                $group->dateValid = strtotime('+' . $slot . ' MONTH', max($group->dateValid, time()));
            }

            if ($this->factoryTasks->specialGroups->save($group)) {
                $query = $this->factoryUsers->notifications->query();
                $query->filter->fieldValue('userId', '=', $this->_application->UserID);
                $query->filter->fieldValue('service', '=', 'special');
                $query->filter->fieldValue('objectId', '=', $group->groupId);
                $it = $query->iteratorForSave();

                foreach ($it as $notification) {
                    $notification->status = 2;
                    $this->factoryUsers->notifications->save($notification);
                }
                $this->factoryTasks->specials->save($this->_special);
            }

            return $this->_response->setJson(['success' => true, 'reload' => true]);
        }

        $group = $this->factoryTasks->specialGroups->getNew();
        $group->title = $item['name'];
        $group->photo = $item['photo_50'];
        $group->ownerId = strval($item['id']);
        $group->specialId = $this->factoryTasks->specials->getByUserIdIsActive($this->_application->UserID,
            true)->specialId;
        $group->userId = $this->_application->UserID;
        $group->dateValid = strtotime('+' . $slot . ' MONTH');
        $group->url = 'https://vk.com/' . $item['screen_name'];

        if ($isFree) {
            $config = \Service\Orders\Model_Config::getSettings();
            $group->isFree = true;
            $group->wasFree = true;
            $group->isFreeCount = intval($config['special']['free']);
        }

        if ($this->factoryTasks->specialGroups->save($group)) {
            $this->factoryTasks->specials->save($this->_special);

            if ($isFree) {
                $this->_application->User->makeShadow();
                $this->_application->User->isFree = $this->_application->User->isFree | 16;
                $this->factoryUsers->users->save($this->_application->User);
            }

            return $this->_response->setJson(['success' => true, 'reload' => true]);
        }

        return $this->_response->setJson(['success' => false, 'errorText' => 'Ошибка сервера']);
    }
}
