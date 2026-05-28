<?php

namespace Service\Tasks;

class Controller_State_Admin_Special_List extends Controller_State_Admin
{
    protected $_url = '/admin/tasks/special/list?p=@p@';

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

        if (isset($this->_application->menu['tasks']['menu']['all'])) {
            $this->_application->menu['tasks']['menu']['all']['active'] = true;
        }

        return null;
    }

    public function actionGet()
    {
        $page = $this->_request->get['p']->int(1);

        $query = $this->factoryTasks->tasks->query()->limit($this->_limit)->offset($this->_limit * ($page - 1))->sort('taskId',
            'DESC')->sqlCalcFoundRows(true);
        $query->filter->fieldValue('isDel', '=', false);

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

        $pageslink = \Lib_Html::GetNavigationPagesNumber(
            $this->_limit,
            4,
            $total,
            $page,
            '/admin/tasks/special/list?p=@p@',
            1
        );

        $vars = [
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
    }

    public function actionPost()
    {
        return $this->_response->setStatus(\System\HttpResponse::S4_METHOD_NOT_ALLOWED);
    }
}
