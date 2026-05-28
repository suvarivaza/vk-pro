<?php

namespace Service\Orders;

class Controller_State_Admin_Default extends Controller_State_Admin
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        if (isset($this->_application->menu['orders']['menu']['default'])) {
            $this->_application->menu['orders']['menu']['default']['active'] = true;
        }

        return null;
    }

    public function actionGet()
    {
        if ($this->_request->get['del']->int(0) > 0) {
            $pack = $this->factoryOrders->orders->packs->getById($this->_request->get['del']->int(0));
            $this->factoryOrders->orders->packs->delete($pack);

            return $this->_response->setLocation($this->_request->server['DOCUMENT_URI']->string());
        }
        $query = $this->factoryOrders->orders->packs->query()->limit(1000)->sort('price', 'ASC');
        $it = $query->iterator();
        $vars = [
            'list' => $it,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/default', $vars));
    }
}
