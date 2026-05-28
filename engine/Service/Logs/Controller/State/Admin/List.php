<?php

namespace Service\Logs;

use Lib_Html;
use STPL;
use System\HttpResponse;

class Controller_State_Admin_List extends Controller_State_Admin
{
    /**
     * @return HttpResponse|null
     */
    public function actionGet()
    {


        ini_set('memory_limit', '2048M');
        set_time_limit(0);


        $logs = $this->factory->getLogs();

        $filters = $this->_getFilter();

        $query = $logs->query();


        if (!empty($filters['action'])) {
            $query->filter->fieldValue('action', '=', $filters['action']);
        }

        if (!empty($filters['objectId'])) {
            $query->filter->fieldValue('objectId', '=', $filters['objectId']);
        }

        if (!empty($filters['userId'])) {
            $query->filter->fieldValue('userId', '=', $filters['userId']);
        }

        if (!empty($filters['login'])) {
            $query->filter->fieldValue('params', 'LIKE',
                '%"login";s:' . strlen($filters['login']) . ':"' . $filters['login'] . '";%');
        }


        //Ограничение на количество выбираемых записей (без этого падает тк слишком много записей)
        //$query->filter->fieldValue('logId', '>', 100000);


        //Вывод логов за сегодня
        //$date_today = strtotime(date('Y-m-d'));
        //$query->filter->fieldValue('date', '>=', $date_today);

        $query->limit($this->_limit)
            ->offset($this->_limit * ($this->_params['page'] - 1))
            ->sqlCalcFoundRows(true) //вот это нужно заменить! ооочень сильно замедляет запрос!
            ->sort('logId', 'DESC');


        $it = $query->iterator();
        $get_params = '?';

        foreach ($filters as $_id => $_filter) {
            $get_params .= $_id . '=' . $_filter . '&';
        }


        $pageslink = Lib_Html::GetNavigationPagesNumber(
            $this->_limit,
            10,
            $it->getTotal(), //по другому нужно получать общее количество записей или разобраться почему так сильно замедляет запрос sqlCalcFoundRows!
            $this->_params['page'],
            '/admin/logs/list/@p@' . $get_params,
            1
        );

        $vars = [
            'list' => $it,
            'pageslink' => $pageslink,
            'filter' => $filters,
        ];

        return $this->_response->setBody(STPL::Fetch('admin/list', $vars));
    }

    private function _getFilter()
    {
        $filter = [
            'action' => '',
            'login' => '',
            'objectId' => '',
            'userId' => '',
        ];

        if (!$this->_request->get['reset_filter']->int(0)) {
            $filter['action'] = $this->_request->get['action']->string('');
            $filter['login'] = $this->_request->get['login']->string('');
            $filter['objectId'] = $this->_request->get['objectId']->int('');
            $filter['userId'] = $this->_request->get['userId']->int('');
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
}
