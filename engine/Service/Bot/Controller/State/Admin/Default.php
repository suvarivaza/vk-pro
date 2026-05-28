<?php

namespace Service\Bot;

class Controller_State_Admin_Default extends Controller_State_Admin
{
    public function actionGet()
    {
        $from = $this->_request->get['dateFrom']->dateTime(strtotime('-30 DAY'));
        $to = $this->_request->get['dateTo']->dateTime(time());

        $counts = $this->factoryBot->bots->getCounts();
        $stat = $this->factoryBot->bots->getStatByDay($from, $to);
        $countsType = $this->factoryBot->bots->getStatByDayType($from, $to);

        $time = $to;
        $keys = [];

        while ($time > $from) {
            $keys[date('Y-m-d', $time)] = date('d.m.Y', $time);
            $time -= 86400;
        }

        $vars = [
            'keys' => $keys,
            'counts' => $counts,
            'stat' => $stat,
            'countsType' => $countsType,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/default', $vars));
    }

    public function actionPost()
    {
        // TODO: Implement actionPost() method.
    }
}
