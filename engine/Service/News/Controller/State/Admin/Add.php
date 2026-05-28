<?php

namespace Service\News;

use Lib_Uuid;
use STPL;
use System\HttpResponse;

class Controller_State_Admin_Add extends Controller_State_Admin
{
    public function actionPrepare()
    {
        parent::actionPrepare();

        $this->_new = $this->factoryNews->news->getNew();

        $this->_application->Title->addScripts(
            [
                '/js/jquery/tinymce/tinymce.min.js',
                '/js/plupload/plupload.full.min.js',
                '/js/plupload/uploader.js',
            ]
        );

        $this->_application->Title->addStyles([
            '/js/jquery/tinymce/plugins/moxiemanager/skins/lightgray/skin.min.css',
            '/css/uploader.css',
        ]);

        if (isset($this->_application->menu['news']['menu']['add'])) {
            $this->_application->menu['news']['menu']['add']['active'] = true;
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function actionGet()
    {
        $vars = [
            'action' => 'add',
            'new' => $this->_new,
            'uploader' => [
                'url' => $this->_request->server['REQUEST_URI']->string(),
                'uuid' => Lib_Uuid::getNext(),
            ],
            'errors' => $this->_errors,
        ];

        return $this->_response->setBody(STPL::Fetch('admin/edit', $vars));
    }

    /**
     * @return HttpResponse|null
     */
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
