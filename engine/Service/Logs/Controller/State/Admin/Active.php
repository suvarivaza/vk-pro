<?php

namespace Service\Logs;

use Database_Logs;
use Lib_DateTime;
use Lib_DB_Factory;
use PDO;
use STPL;
use System\HttpResponse;

class Controller_State_Admin_Active extends Controller_State_Admin
{
    private $_filter = [];

    /**
     * @return HttpResponse|null
     */
    public function actionGet()
    {

        $user = $this->factoryUsers->users->getById((int) $this->_params['userId']);

        $this->_application->Title->Title = 'Статистика пользователя ' . $user->name;

        $this->_application->Title->addScripts([
            '/scripts/highchart/highcharts.js',
            '/scripts/highchart/themes/sand-signika.js',
        ]);

        $this->getFilter();

        $vars = [
            'period' => Lib_DateTime::$month[$this->_filter['month']] . ' ' . $this->_filter['year'],
            'month' => Lib_DateTime::$month,
            'filter' => $this->_filter,
            'user' => $user->name,
            'titles' => [],
        ];

        $charts = [
            Model_Config::CATALOG_SEARCH_ITEMS => [
                'title' => 'Поиск предложений по номеру и производителю',
            ],
            Model_Config::BASKET_ADD_ORDER => [
                'title' => 'Добавление заказа',
            ],
            Model_Config::BASKET_ADD_ITEM_TO_ORDER => [
                'title' => 'Добавление позиции в заказ',
            ],
        ];

        $base = new Database_Logs();
        $db = Lib_DB_Factory::GetInstance($base);

        $actions = [
            Model_Config::CATALOG_SEARCH_ITEMS,
            Model_Config::BASKET_ADD_ORDER,
            Model_Config::BASKET_ADD_ITEM_TO_ORDER,
        ];
        $table = 'log_' . $this->_filter['year'] . '_' . $this->_filter['month'];
        $sql = "select DATE(`date`) as date_str, count(`logId`) as 'cnt', `action` from `" . $table . '`';
        $sql .= ' WHERE `userId` = ' . $this->_params['userId'];
        $sql .= " AND `action` IN ('" . implode('\',\'', $actions) . "')";
        $sql .= ' GROUP BY date_str, `action`';
        $query = $db->query($sql);
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $key = strtotime($row['date_str']);
            $_date = date('d.m.Y', strtotime($row['date_str']));

            foreach ($actions as $action) {
                if (!isset($charts[$action]['list'][$key])) {
                    $charts[$action]['list'][$key] = 0;
                }
            }

            $charts[$row['action']]['list'][$key] = $row['cnt'];

            if (!isset($vars['titles'][$key])) {
                $vars['titles'][$key] = $_date;
            }
        }

        $vars['charts'] = $charts;

        for ($year = date('Y'); $year > date('Y') - 5; $year--) {
            $vars['year'][$year] = $year;
        }

        return $this->_response->setBody(STPL::Fetch('admin/active', $vars));
    }

    private function getFilter()
    {
        $this->_filter = [
            'month' => str_pad(date('m'), 2, '0', STR_PAD_LEFT),
            'year' => date('Y'),
        ];

        if ($this->_request->get['reset_filter']->int(0)) {
            return null;
        }

        if ($this->_request->get['month']->int(0)) {
            $this->_filter['month'] = str_pad($this->_request->get['month']->int(0), 2, '0', STR_PAD_LEFT);
        }

        if ($this->_request->get['year']->int(0)) {
            $this->_filter['year'] = $this->_request->get['year']->int(0);
        }
    }

    /**
     * @return HttpResponse|null
     */
    public function actionPost()
    {
        // TODO: Implement actionPost() method.
    }
}
