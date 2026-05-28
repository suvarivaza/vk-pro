<?php

namespace Service\Users;

abstract class Controller_State_Admin extends Controller_State
{
    /** @var Model_Users_User|null */
    protected $_user = null;

    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if (isset($this->_application->menu['users'])) {
            $this->_application->menu['users']['active'] = true;
        }

        if (null !== $response) {
            return $response;
        }

        $this->_application->Title->addScript('/js/admin/user.min.js');

        return null;
    }

    /**
     * @return \System\HttpResponse|null
     */
    public function actionPost()
    {
        return $this->_response->setStatus(\System\HttpResponse::S4_METHOD_NOT_ALLOWED);
    }
}
