<?php

namespace Service\System;

class Controller_Admin_Default extends Controller_Admin
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if (null !== $response) {
            return $response;
        }

        return $this->_response->setLocation('/admin/system/settings');
    }

    /**
     * @return \System\HttpResponse|null
     */
    public function actionGet()
    {
    }

    /**
     * @return \System\HttpResponse|null
     */
    public function actionPost()
    {
    }
}
