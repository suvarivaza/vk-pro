<?php

namespace Service\Admin;

/**
 * Class Controller_State_Default
 *
 * @package Service\Admin
 */
class Controller_State_Start extends \System\Service_Controller_State
{
    public function actionPrepare()
    {
        $this->_application->Title->addScripts([
            '/js/jquery/tinymce/tinymce.min.js',
        ]);

        $this->_application->admin = true;
        $this->_application->menu = Model_Config::$menu;

        return parent::actionPrepare();
    }

    /**
     * @return \System\HttpResponse|null
     */
    public function actionGet()
    {
        if ($this->_request->get['new']->int(0) == 1) {
            echo $this->_response->setBody(\STPL::Fetch('new/index',
                ['app' => $this->_application, 'name' => $this->_application->User->name]));
            exit;
        }
        echo $this->_response->setBody(\STPL::Fetch('start',
            ['app' => $this->_application, 'name' => $this->_application->User->name]));
        exit;
    }

    /**
     * @return \System\HttpResponse|null
     */
    public function actionPost()
    {

        return null;
    }
}
