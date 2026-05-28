<?php

namespace Service\Tasks;

class Controller_State_Admin_Stat_Default extends Controller_State_Admin
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        if (isset($this->_application->menu['tasks']['menu']['stat'])) {
            $this->_application->menu['tasks']['menu']['stat']['active'] = true;
        }

        return null;
    }

    public function actionGet()
    {
        $stat = $this->factoryTasks->tasks->getStatDefault();

        $from = $this->_request->get['dateFrom']->dateTime(strtotime('-30 DAY'));
        $to = $this->_request->get['dateTo']->dateTime(time());

        $all = $this->factoryTasks->tasks->getStatByDay($from, $to);
        $del = $this->factoryTasks->tasks->getStatByDay($from, $to, true);
        $active = $this->factoryTasks->tasks->getStatByDay($from, $to, false, true);
        $done = $this->factoryTasks->tasks->getStatByDay($from, $to, false, false, true);

        $time = $to;
        $keys = [];

        while ($time > $from) {
            $keys[date('Y-m-d', $time)] = date('d.m.Y', $time);
            $time -= 86400;
        }

        $vars = [
            'types' => Model_Config::$types,
            'from' => $from,
            'to' => $to,
            'keys' => $keys,
            'stat' => $stat,
            'all' => $all,
            'del' => $del,
            'active' => $active,
            'done' => $done,
        ];

        $html = \STPL::Fetch('admin/stat/default', $vars);

        return $this->_response->setBody($html);
    }

    public function actionPost()
    {
        return null;
    }
}
