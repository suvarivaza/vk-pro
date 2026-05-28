<?php

namespace Service\Users;

class Controller_State_Admin_Default extends Controller_State_Admin
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        if (isset($this->_application->menu['users']['menu']['default'])) {
            $this->_application->menu['users']['menu']['default']['active'] = true;
        }

        return null;
    }

    public function actionGet()
    {
        $total = $this->factoryUsers->users->getCountTotal();
        $onlineTotal = $this->factoryUsers->users->getCountTotalOnline();
        $banTotal = $this->factoryUsers->users->getCountTotalBan();
        $badTotal = $this->factoryUsers->users->getCountTotalBad();

        $from = $this->_request->get['dateFrom']->dateTime(strtotime('-30 DAY'));
        $to = $this->_request->get['dateTo']->dateTime(time());

        $stat = $this->factoryUsers->users->getStatByDay($from, $to);
        $bad = $this->factoryUsers->users->getStatByDay($from, $to, true);
        $ban = $this->factoryUsers->users->getStatByDay($from, $to, false, true);
        $online = [];

        $time = $to;
        $keys = [];

        while ($time > $from) {
            $keys[date('Y-m-d', $time)] = date('d.m.Y', $time);
            $time -= 86400;
        }

        foreach ($keys as $key => $title) {
            $obj = $this->factoryUsers->online->getByKey($key);

            if ($obj !== null) {
                $online[$key] = $obj->count;
            } else {
                $online[$key] = 0;
            }
        }
        $vars = [
            'from' => $from,
            'to' => $to,
            'keys' => $keys,
            'stat' => $stat,
            'bad' => $bad,
            'ban' => $ban,
            'online' => $online,
            'total' => [
                'total' => $total,
                'online' => $onlineTotal,
                'ban' => $banTotal,
                'bad' => $badTotal,
            ],
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/default', $vars));
    }
}
