<?php

namespace Service\Tasks;

class Controller_State_Client_Special_List extends Controller_State_Client_Special_Default
{
    private $_penatly = null;

    public function actionPrepare()
    {
        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setLocation('/users/login');
        }

        if ($this->_application->User->login == '') {
            return $this->_response->setLocation('/users/register');
        }

        $this->_application->Title->addScript('/js/dialog.js');

        $this->_group = $this->factoryTasks->specialGroups->getById($this->_params['groupId']);

        if ($this->_group === null) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }

        if ($this->_group->userId != $this->_application->UserID) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }

        if (isset($this->_request->get['toggle'])) {
            $task = $this->factoryTasks->tasks->getById($this->_request->get['toggle']->int(0), true);

            if ($task === null || $task->userId != $this->_application->UserID) {
                return $this->_response->setStatus(\System\HttpResponse::S4_BAD_REQUEST);
            }
            $task->active = !$task->active;
            $this->factoryTasks->tasks->save($task);

            return $this->_response->setLocation($this->_request->server['REDIRECT_URL']->string());
        }

        if (isset($this->_request->get['isDel'])) {
            $task = $this->factoryTasks->tasks->getById($this->_request->get['isDel']->int(0), true);

            if ($task === null || $task->userId != $this->_application->UserID) {
                return $this->_response->setStatus(\System\HttpResponse::S4_BAD_REQUEST);
            }
            $task->isDel = true;
            $task->isDelDate = time();

            if ($this->factoryTasks->tasks->save($task)) {
                $balance = $task->price * $task->countRemain;
                $task->countRemain = 0;
                $task->countReady = $task->count;
                $this->_application->User->makeShadow();
                $this->_application->User->balance += $balance;

                if ($this->factoryUsers->users->save($this->_application->User)) {
                    $this->factoryTasks->tasks->save($task);
                }
            }

            return $this->_response->setLocation($this->_request->server['REDIRECT_URL']->string());
        }

        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        $this->_application->Title->Title = 'Спецзадания - ' . $this->_group->title;

        return null;
    }

    public function actionGet()
    {
        $query = $this->factoryTasks->tasks->query()
            ->sort('taskId', 'DESC')
            ->limit(50)
            ->offset(($this->_params['page'] - 1) * 50)
            ->sqlCalcFoundRows(true);

        $query->filter
            ->fieldValue('userId', '=', $this->_application->UserID)
            ->fieldValue('specialId', '=', intval($this->_params['groupId']))
            ->fieldValue('isDel', '=', false);

        if ($this->_params['type'] != 'all') {
            $query->filter->fieldValue('type', '=', $this->_params['type']);
        }

        $it = $query->iterator();
        $list = [];

        foreach ($it as $task) {
            $list[] = $task;
        }
        $vars = [
            'counts' => [
                'total' => $this->factoryTasks->specialGroups->users->getCountTotal($this->_group->groupId),
                'online' => $this->factoryTasks->specialGroups->users->getCountOnline($this->_group->groupId),
            ],
            'types' => $this->_types,
            'type' => $this->_params['type'],
            'list' => $list,
            'user' => $this->_application->User,
            'group' => $this->_group,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/special/list', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'showUpdateForm':
                return $this->_showUpdateForm();
            case 'update':
                return $this->_update();
        }

        return parent::actionPost();
    }

    private function _showUpdateForm()
    {
        $vars = [
            'group' => $this->_group,
        ];
        $html = \STPL::Fetch('client/special/list/update_form', $vars);

        return $this->_response->setJson([
            'success' => true,
            'title' => 'Обновление данных по подписчикам',
            'html' => $html,
        ]);
    }

    private function _update()
    {
        $this->_penatly = json_decode(file_get_contents(\Service\Users\Model_Config::$karmaPath), true);
        $this->_penatly = $this->_penatly['penatly'];

        if (time() - $this->_group->lastUpdate < 86400) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => 'Запускать проверку можно не чаще, чем раз в сутки',
            ]);
        }

        $page = 0;
        $list = [];
        do {
            $count = 0;

            $offset = 1000 * $page;
            $json = $this->VK->getMembers($this->_group->ownerId, $this->check_access_token, 1000, $offset);

            $page++;
            $total = $json['count'];

            foreach ($json['items'] as $data) {
                $count++;
                $list[$data['id']] = $data;
            }
            sleep(1);
        } while ($count && (1000 * $page < $total));

        $internal = [];
        $users = $this->factoryTasks->specialGroups->users->getBySpecialId($this->_group->groupId);
        /** @var Model_Specials_Users_User $suser */
        foreach ($users as $suser) {
            $user = $this->factoryUsers->users->getById($suser->userId);

            if ($user !== null) {
                $internal[$user->uid] = $suser;
            }
        }

        $deleted = 0;

        foreach ($internal as $uid => $suser) {
            if (!isset($list[$uid])) {
                ++$deleted;
                $this->factoryTasks->specialGroups->users->delete($suser);
            }

            if (isset($list[$uid]['deactivated'])) {
                ++$deleted;
                $this->factoryTasks->specialGroups->users->delete($suser);
            }
        }
    }

    protected function _sort_tasks($a, $b)
    {
        return $a->dateCreate < $b->dateCreate;
    }


    private function _checkJoin(Model_Tasks_Task $task, $taskUsers)
    {

        /** @var \Service\Tasks\Model_Users_User $taskUser */
        $uids = [];
        $tasks = [];

        foreach ($taskUsers as $taskUser) {
            $tasks[$taskUser->uid] = $taskUser;
            $uids[] = $taskUser->uid;
        }
        $offet = 0;
        $limit = 500;

        while ($offet < count($uids)) {

            $user_ids = implode(',', array_slice($uids, $offet, $limit));
            $response = $this->VK->isMembers($task->ownerId, $user_ids, $this->check_access_token);
            usleep(300000);

            $offet += $limit;

            foreach ($response as $id => $row) {
                if ($id === 'error') {
                    continue;
                }

                if (!$row['member']) {
                    $user = $this->factoryUsers->users->getByUid($row['user_id'], true);
                    $karma = $this->factoryUsers->users->karma->getNew();
                    $karma->userId = $user->userId;
                    $karma->dateCreate = time();
                    $karma->karma = -floatval($this->_penatly[$task->type]);
                    $karma->karmaFrom = $user->karma;
                    $user->karma -= $this->_penatly[$task->type];
                    $karma->taskId = $task->taskId;
                    $karma->karmaTo = $user->karma;
                    $karma->comment = 'Отписался от группы';
                    $this->factoryUsers->users->karma->save($karma);

                    $balance = $this->factoryUsers->users->balance->getNew();
                    $balance->userId = $user->userId;
                    $balance->isPenalty = true;
                    $balance->balance = -floatval($task->price);
                    $balance->balanceFrom = $user->balance;
                    $user->balance += $balance->balance;
                    $balance->balanceTo = $user->balance;
                    $balance->dateCreate = time();
                    $balance->comment = 'Отписался от группы';
                    $this->factoryUsers->users->balance->save($balance);

                    $this->factoryUsers->users->save($user);

                    $taskUser = $tasks[$row['user_id']];

                    $taskUser->makeShadow();
                    $taskUser->isDoneDate = null;
                    $taskUser->isDone = false;
                    $taskUser->isDel = true;
                    $taskUser->isDelDate = time();
                    $this->factoryTasks->users->save($taskUser);

                    $author = $this->factoryUsers->users->getById($task->userId, true);
                    $balance = $this->factoryUsers->users->balance->getNew();
                    $balance->userId = $author->userId;
                    $balance->isCompensation = true;
                    $balance->balance = floatval($task->price);
                    $balance->balanceFrom = $author->balance;
                    $author->balance += $balance->balance;
                    $balance->balanceTo = $author->balance;
                    $balance->dateCreate = time();
                    $balance->comment = 'Компенсация за отписку от группы';
                    $this->factoryUsers->users->balance->save($balance);
                    $this->factoryUsers->users->save($author);

                    $task->makeShadow();
                    $task->countReady--;
                    $task->countRemain = $task->count - $task->countReady;
                    $this->factoryTasks->tasks->save($task);
                }
            }
        }
    }
}
