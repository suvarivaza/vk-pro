<?php

namespace Service\Auto;

class Controller_State_Client_List extends Controller_State_Client
{
    public function actionGet()
    {
        $auto = $this->factoryAuto->auto->getById($this->_params['autoId']);

        if ($auto === null || $auto->userId != $this->_application->UserID) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }

        $templates = $this->factoryAuto->auto->templates->getByAutoId($auto->autoId);
        $vars = [
            'auto' => $auto,
            'templates' => $templates,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/list', $vars));
    }

    public function actionPost()
    {
        return $this->_response->setStatus(\System\HttpResponse::S4_METHOD_NOT_ALLOWED);
    }
}
