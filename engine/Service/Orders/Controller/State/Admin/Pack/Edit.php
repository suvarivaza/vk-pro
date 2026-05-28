<?php

namespace Service\Orders;

class Controller_State_Admin_Pack_Edit extends Controller_State_Admin
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        $this->_pack = $this->factoryOrders->orders->packs->getById($this->_params['packId'], true);

        $this->_application->Title->addStyles(['/css/material-switch.min.css']);

        return null;
    }

    public function actionGet()
    {
        $vars = [
            'errors' => $this->_errors,
            'title' => 'Редаткирование <strong>' . $this->_pack->title . '</strong>',
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
