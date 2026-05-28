<?php

namespace Service\Tasks;

class Controller_State_Admin_Default extends Controller_State_Admin
{
    protected $_url = '/admin/tasks?p=@p@';
    protected $_title = 'Все задания';

    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        if (isset($this->_request->get['toggle'])) {
            $task = $this->factoryTasks->tasks->getById($this->_request->get['toggle']->int(0), true);

            if ($task === null) {
                return $this->_response->setStatus(\System\HttpResponse::S4_BAD_REQUEST);
            }
            $task->active = !$task->active;
            $this->factoryTasks->tasks->save($task);

            return $this->_response->setLocation($this->_request->server['REDIRECT_URL']->string() . '?p=' . $this->_request->get['p']->int(1));
        }

        if (isset($this->_request->get['isDel'])) {

            $task = $this->factoryTasks->tasks->getById($this->_request->get['isDel']->int(0), true);

            if ($task === null) {
                return $this->_response->setStatus(\System\HttpResponse::S4_BAD_REQUEST);
            }

            $task->isDel = true;
            $task->isDelDate = time();

            if ($this->factoryTasks->tasks->save($task)) {
                $user = $this->factoryUsers->users->getById($task->userId, true);
                $sum = $task->price * $task->countRemain;
                $user->balance += $sum;
                $this->factoryUsers->users->save($user);
            }

            return $this->_response->setLocation($this->_request->server['REDIRECT_URL']->string() . '?p=' . $this->_request->get['p']->int(1));
        }

        if(isset($this->_request->get['deleteTask'])){

            $task = $this->factoryTasks->tasks->getById($this->_request->get['deleteTask']->int(0), true);

            if (!$task) {
                return $this->_response->setStatus(\System\HttpResponse::S4_BAD_REQUEST);
            }

            $this->factoryTasks->tasks->delete($task);
        }

        if (isset($this->_application->menu['tasks']['menu']['all'])) {
            $this->_application->menu['tasks']['menu']['all']['active'] = true;
        }

        return null;
    }

    public function actionGet()
    {
        $page = $this->_request->get['p']->int(1);

        $query = $this->factoryTasks->tasks->query()->limit($this->_limit)->offset($this->_limit * ($page - 1))->sqlCalcFoundRows(true);

        $this->_filter($query);
        $it = $query->iterator();
        $total = $it->getTotal();

        $list = [];
        $userIds = [];

        foreach ($it as $task) {
            $list[] = $task;
            $userIds[$task->userId] = $task->userId;
        }
        $users = [];

        if (count($userIds)) {
            $query = $this->factoryUsers->users->query();
            $query->filter->fieldCollection('userId', 'IN', $userIds);
            $it = $query->iterator();

            foreach ($it as $user) {
                $users[$user->userId] = $user;
            }
        }

        $filter = [
            'taskId' => $this->_request->get['taskId']->int(0),
            'userId' => $this->_request->get['userId']->int(0),
            'type' => $this->_request->get['type']->string(''),
            'q' => $this->_request->get['q']->string(''),
            'sort' => $this->_request->get['sort']->string('taskId'),
            'dir' => $this->_request->get['dir']->string('DESC'),
        ];

        $pageslink = \Lib_Html::GetNavigationPagesNumber(
            $this->_limit,
            10,
            $total,
            $page,
            $this->_url . '&' . http_build_query($filter),
            1
        );

        $vars = [
            'filter' => $filter,
            'title' => $this->_title,
            'types' => $this->_types,
            'page' => $page,
            'list' => $list,
            'users' => $users,
            'pageslink' => $pageslink,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/default', $vars));
    }

    protected function _filter(\Lib_ORM_Query $query)
    {
        $filter = [
            'taskId' => $this->_request->get['taskId']->int(0),
            'userId' => $this->_request->get['userId']->int(0),
            'type' => $this->_request->get['type']->string(''),
            'q' => $this->_request->get['q']->string(''),
            'sort' => $this->_request->get['sort']->string('taskId'),
            'dir' => $this->_request->get['dir']->string('DESC'),
        ];

        $query->sort($filter['sort'], $filter['dir']);

        if ($filter['taskId']) {
            $query->filter->fieldValue('taskId', '=', $filter['taskId']);
        }

        if ($filter['userId']) {
            $query->filter->fieldValue('userId', '=', $filter['userId']);
        }

        if ($filter['type']) {
            $query->filter->fieldValue('type', '=', $filter['type']);
        }

        if ($filter['q']) {
            $query->filter->aggregatorOpen('OR')
                ->fieldValue('url', 'LIKE', '%' . $filter['q'] . '%')
                ->fieldValue('title', 'LIKE', '%' . $filter['q'] . '%')
                ->aggregatorClose();
        }
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string('');

        switch ($action) {
            case 'showTaskForm':
                return $this->_showTaskForm();
        }

        return $this->_response->setStatus(\System\HttpResponse::S4_METHOD_NOT_ALLOWED);
    }

    private function _showTaskForm()
    {
        $task = $this->factoryTasks->tasks->getById($this->_request->post['taskId']->int(0));

        if ($task === null) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Не удалось найти задание']);
        }

        $vars = [
            'app' => $this->_application,
            'task' => $task,
            'types' => Model_Config::$types,
            'vkTypes' => Model_Config::$vkTypes,
            'targeting' => Model_Config::$targeting,
        ];

        $html = \STPL::Fetch('admin/task_form', $vars);

        return $this->_response->setJson(['success' => true, 'title' => 'Задание ' . $task->title, 'html' => $html]);
    }
}
