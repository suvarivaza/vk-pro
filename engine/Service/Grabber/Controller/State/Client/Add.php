<?php

namespace Service\Grabber;

class Controller_State_Client_Add extends Controller_State_Client
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }
        $this->_group = $this->factoryGrabber->groups->getById($this->_params['groupId']);

        if ($this->_group === null || $this->_group->userId != $this->_application->UserID) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }
    }

    public function actionGet()
    {
        $vars = [
            'action' => 'add',
            'errors' => $this->_errors,
            'grabber' => $this->_grabberGroup,
            'group' => $this->_group,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/edit', $vars));
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
