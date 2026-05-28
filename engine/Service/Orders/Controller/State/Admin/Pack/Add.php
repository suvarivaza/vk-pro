<?php

namespace Service\Orders;

class Controller_State_Admin_Pack_Add extends Controller_State_Admin
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        $this->_pack = $this->factoryOrders->orders->packs->getNew();

        $this->_application->Title->addStyles(['/css/material-switch.min.css']);

        if (isset($this->_application->menu['orders']['menu']['add'])) {
            $this->_application->menu['orders']['menu']['add']['active'] = true;
        }

        return null;
    }

    public function actionGet()
    {
        $vars = [
            'errors' => $this->_errors,
            'title' => 'Добавление пакета услуг',
            'action' => 'add',
            'pack' => $this->_pack,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/pack/edit', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'add':
                return $this->_edit();
        }

        return parent::actionPost();
    }
}
