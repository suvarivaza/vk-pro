<?php

namespace Service\Messages;

class Controller_State_Admin_Add extends Controller_State_Admin
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        $this->_message = $this->factoryMessages->messages->getById($this->_params['messageId'], true);

        return null;
    }

    public function actionGet()
    {
        $vars = [
            'errors' => $this->_errors,
            'message' => $this->_message,
            'action' => 'edit',
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/edit', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'edit':
                return $this->_edit();
        }
    }
}
