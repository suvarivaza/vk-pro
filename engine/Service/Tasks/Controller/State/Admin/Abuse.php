<?php

namespace Service\Tasks;

class Controller_State_Admin_Abuse extends Controller_State_Admin
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        $isDone = $this->_request->get['isDone']->int(0);

        if ($isDone > 0) {
            $it = $this->factoryTasks->abuses->getByTaskId($isDone, true);

            foreach ($it as $abuse) {
                $abuse->isDone = true;
                $this->factoryTasks->abuses->save($abuse);
            }

            return $this->_response->setLocation($this->_request->server['REDIRECT_URL']->string());
        }

        $del = $this->_request->get['del']->int(0);

        if ($del > 0) {
            $task = $this->factoryTasks->tasks->getById($del, true);
            $task->isDel = true;
            $task->isDelDate = time();

            if ($this->factoryTasks->tasks->save($task)) {
                $it = $this->factoryTasks->abuses->getByTaskId($del, true);

                foreach ($it as $abuse) {
                    $abuse->isDone = true;
                    $this->factoryTasks->abuses->save($abuse);
                }
            }

            return $this->_response->setLocation($this->_request->server['REDIRECT_URL']->string());
        }

        $ban = $this->_request->get['ban']->int(0);

        if ($ban > 0) {
            $task = $this->factoryTasks->tasks->getById($ban, true);
            $user = $this->factoryUsers->users->getById($task->userId, true);
            $user->ban = true;
            $task->isDel = true;
            $task->isDelDate = time();

            if ($this->factoryTasks->tasks->save($task)) {
                if ($this->factoryUsers->users->save($user)) {
                    $it = $this->factoryTasks->abuses->getByTaskId($ban, true);

                    foreach ($it as $abuse) {
                        $abuse->isDone = true;
                        $this->factoryTasks->abuses->save($abuse);
                    }
                }
            }

            return $this->_response->setLocation($this->_request->server['REDIRECT_URL']->string());
        }

        if (isset($this->_application->menu['tasks']['menu']['abuse'])) {
            $this->_application->menu['tasks']['menu']['abuse']['active'] = true;
        }

        return null;
    }

    public function actionGet()
    {
        if (isset($this->_application->menu['abuse'])) {
            $this->_application->menu['abuse']['active'] = true;
        }

        $query = $this->factoryTasks->abuses->query();
        $query->filter->fieldValue('isDone', '=', false);
        $query->sort('abuseId', 'DESC');

        $it = $query->iterator();
        $list = [];
        /** @var Model_Abuses_Abuse $abuse */
        foreach ($it as $abuse) {
            $list[$abuse->taskId][] = $abuse;
        }

        $tasks = [];

        if (count($list)) {
            $query = $this->factoryTasks->tasks->query()->sort('taskId', 'DESC');
            $query->filter->fieldCollection('taskId', 'IN', array_keys($list));
            $it = $query->iterator();

            foreach ($it as $task) {
                $tasks[$task->taskId] = $task;
            }
        }

        $vars = [
            'types' => $this->_types,
            'reasons' => Model_Config::$reasons,
            'list' => $list,
            'tasks' => $tasks,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/abuse', $vars));
    }

    public function actionPost()
    {
    }
}
