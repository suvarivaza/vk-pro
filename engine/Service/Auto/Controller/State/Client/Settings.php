<?php

namespace Service\Auto;

class Controller_State_Client_Settings extends Controller_State_Client
{
    public function actionGet()
    {
        $auto = $this->factoryAuto->auto->getByUserIdIsActive($this->_application->UserID, true);

        if ($auto === null) {
            return $this->_response->setLocation('/auto/buy');
        }

        $groups = $this->factoryAuto->auto->groups->getByAutoId($auto->autoId);

        $vars = [
            'auto' => $auto,
            'groups' => $groups,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/settings', $vars));
    }

    public function actionPost()
    {
        return $this->_response->setStatus(\System\HttpResponse::S4_METHOD_NOT_ALLOWED);
    }
}
