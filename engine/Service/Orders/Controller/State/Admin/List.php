<?php

namespace Service\Orders;

class Controller_State_Admin_List extends Controller_State_Admin
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        if (isset($this->_application->menu['orders']['menu']['list'])) {
            $this->_application->menu['orders']['menu']['list']['active'] = true;
        }

        return null;
    }

    public function actionGet()
    {
        $page = $this->_params['page'];
        $query = $this->factoryOrders->orders->packs->query()->limit(100)->offset(100 * ($page - 1))->sort('price',
            'ASC');
        $it = $query->iterator();
        $packs = [];

        foreach ($it as $pack) {
            $packs[$pack->packId] = $pack;
        }

        $this->_application->page = 'admin-orders-list';

        $page = $this->_params['page'];

        $query = $this->factoryOrders->orders->query()->limit(100)->offset(100 * ($page - 1))->sort('dateCreate',
            'DESC')->sqlCalcFoundRows(true);

        if ($this->_request->get['userId']->int(0)) {
            $filter['userId'] = $this->_request->get['userId']->int(0);
            $query->filter->fieldValue('userId', '=', $this->_request->get['userId']->int(0));
        }

        if(!$filter) $filter = [];

        $it = $query->iterator();

        $pageslink = \Lib_Html::GetNavigationPagesNumber(
            100,
            4,
            $it->getTotal(),
            $page,
            '/admin/orders/list/@p@?' . http_build_query($filter),
            1
        );

        $list = [];

        foreach ($it as $order) {
            $list[] = $order;
        }

        $settings = json_decode(file_get_contents(Model_Config::$settings), true);

        $vars = [
            'packs' => $packs,
            'list' => $list,
            'pageslink' => $pageslink,
            'errors' => $this->_errors,
            'settings' => $settings,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/list', $vars));
    }
}
