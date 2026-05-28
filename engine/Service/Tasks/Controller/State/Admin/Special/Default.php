<?php

namespace Service\Tasks;

class Controller_State_Admin_Special_Default extends Controller_State_Admin
{
    private $_text = '';

    public function actionPrepare()
    {
        parent::actionPrepare();

        $this->_text = file_get_contents(Model_Config::$specialPath);

        $this->_application->Title->addScripts(
            [
                '/js/jquery/tinymce/tinymce.min.js',
                '/js/plupload/plupload.full.min.js',
                '/js/plupload/uploader.js',
                '/js/bootstrap/bootstrap-datepicker.js',
            ]
        );

        $this->_application->Title->addStyles([
            '/js/jquery/tinymce/plugins/moxiemanager/skins/lightgray/skin.min.css',
            '/css/uploader.css',
        ]);

        if (isset($this->_application->menu['tasks']['menu']['special'])) {
            $this->_application->menu['tasks']['menu']['special']['active'] = true;
        }

        return null;
    }

    public function actionGet()
    {
        $vars = [
            'text' => $this->_text,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/special/default', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'save':
                return $this->_save();
        }
    }

    private function _save()
    {
        $this->_text = $this->_request->post['text']->string();

        file_put_contents(Model_Config::$specialPath, $this->_text);

        return null;
    }
}
