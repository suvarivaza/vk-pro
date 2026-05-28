<?php

namespace Service\System;

class Controller_Admin_Settings_Robot extends Controller_Admin
{
    private $_robot = '';
    private $_reload = false;

    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        $this->_robot = file_get_contents(ENGINE_PATH . 'robots.txt');

        return null;
    }

    public function actionGet()
    {
        $saved = false;

        if ($this->_reload === true) {
            $saved = true;
            $this->_robot = file_get_contents(ENGINE_PATH . 'robots.txt');
        }
        $vars = [
            'saved' => $saved,
            'robot' => $this->_robot,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/settings/robot', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'generate':
                return $this->_generateSitemap();
            case 'save':
                return $this->_save();
        }

        return $this->_response->setStatus(\System\HttpResponse::S4_METHOD_NOT_ALLOWED);
    }

    private function _generateSitemap()
    {
    }

    private function _save()
    {
        $string = $this->_request->post['robot']->string('', \System\HttpRequest::OUT_HTML);
        file_put_contents(ENGINE_PATH . 'robots.txt', $string);
        $this->_reload = true;

        return null;
    }
}
