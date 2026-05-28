<?php

namespace Service\Tasks;

class Controller_State_Client_My extends Controller_State_Client
{
    public function actionPrepare()
    {
        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setLocation('/users/login');
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

            if ($task->isDel === true) {
                return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
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

        $this->_application->page = 'tasks-my';

        $this->_application->Title->add('link', [
            'rel' => 'icon',
            'href' => '/img/icons/32/icon-work.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->add('link', [
            'rel' => 'shortcut icon',
            'href' => '/img/icons/32/icon-work.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->Title = 'Мои задания';

        return parent::actionPrepare();
    }

    public function actionGet()
    {
        $taskId = $this->_request->get['taskId']->int(0);
        $type = $this->_params['type'];
        $page = $this->_params['page'];
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $query = $this->factoryTasks->tasks->query()
            ->sort('taskId', 'DESC')
            ->limit($limit)
            ->offset($offset)
            ->sqlCalcFoundRows(true);

        $query->filter
            ->fieldValue('userId', '=', $this->_application->UserID)
            ->fieldValue('isSpecial', '=', false)
            ->fieldValue('isDel', '=', false);

        if ($type != 'all') {
            $query->filter->fieldValue('type', '=', $type);
        }

        if ($type == 'all' and $taskId) {
            $query->filter->fieldValue('taskId', '=', $taskId);
        }

        $it = $query->iterator();
        $list = [];

        foreach ($it as $task) {
            $list[] = $task;
        }
        $vars = [
            'titles' => $this->_titles,
            'type' => $type,
            'list' => $list,
            'user' => $this->_application->User,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/my', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'add':
                return $this->_edit();
        }
    }
}

