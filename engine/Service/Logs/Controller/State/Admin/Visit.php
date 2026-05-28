<?php

namespace Service\Logs;

use Database_Logs;
use Lib_DB_Factory;
use PDO;
use STPL;
use System\HttpResponse;

class Controller_State_Admin_Visit extends Controller_State_Admin
{
    /**
     * @return HttpResponse|null
     */
    public function actionGet()
    {
        $base = new Database_Logs();
        $db = Lib_DB_Factory::GetInstance($base);

        $filter = $this->getFilter();

        $vars['dateFrom'] = date('d.m.Y', $filter['dateFrom']);
        $vars['dateTo'] = date('d.m.Y', $filter['dateTo']);

        $monthFrom = date('m', $filter['dateFrom']);
        $monthTo = date('m', $filter['dateTo']);

        $month = intval($monthFrom);
        do {
            $table = 'log_' . date('Y', $filter['dateFrom']) . '_' . sprintf('%02d', $month);
            $sql = "select `ip`, `userId`, count(`logId`) as 'cnt' from `" . $table . '`';
            $sql .= " WHERE `action` = '" . Model_Config::CATALOG_SEARCH_ITEMS . "' AND `date` >= '" . date('Y-m-d 00:00:00',
                    $filter['dateFrom']) . "'";
            $sql .= " AND `date` <= '" . date('Y-m-d 23:59:59', $filter['dateTo']) . "'";
            $sql .= ' group by `ip`';
            $query = $db->query($sql);
            $rows = $query->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $row) {
                $log = [
                    'login' => '',
                    'name' => '',
                    'email' => '',
                    'userId' => $row['userId'],
                    'ip' => $row['ip'],
                    'count' => $row['cnt'],
                ];

                if (!empty($row['userId'])) {
                    $user = $this->factoryUsers->users->getById($row['userId']);

                    if (null != $user) {
                    }

                    $log['login'] = $user->login;
                    $log['userName'] = $user->name;
                    $log['email'] = $user->email;
                }

                if (isset($vars['logs'][$row['ip']])) {
                    $vars['logs'][$row['ip']]['count'] += $log['count'];
                } else {
                    $vars['logs'][$row['ip']] = $log;
                }
            }
            $month++;
        } while ($monthTo >= $month);

        uasort($vars['logs'], [$this, 'sortIP']);

        return $this->_response->setBody(STPL::Fetch('admin/visit', $vars));
    }

    private function getFilter()
    {
        $filter['dateFrom'] = $this->_request->get['dateFrom']->string();

        if (empty($filter['dateFrom'])) {
            $filter['dateFrom'] = time();
        } else {
            $filter['dateFrom'] = strtotime($filter['dateFrom']);
        }

        $filter['dateTo'] = $this->_request->get['dateTo']->string();

        if (empty($filter['dateTo'])) {
            $filter['dateTo'] = time();
        } else {
            $filter['dateTo'] = strtotime($filter['dateTo']) + 86399;
        }

        return $filter;
    }

    /**
     * @return HttpResponse|null
     */
    public function actionPost()
    {
        // TODO: Implement actionPost() method.
    }

    protected function sortIP($a, $b)
    {
        if ($a['count'] > $b['count']) {
            return -1;
        } elseif ($a['count'] < $b['count']) {
            return 1;
        }

        return 0;
    }
}
