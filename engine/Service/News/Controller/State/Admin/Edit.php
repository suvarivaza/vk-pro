<?php

namespace Service\News;

use Lib_Uuid;
use STPL;
use System\HttpResponse;

class Controller_State_Admin_Edit extends Controller_State_Admin
{
    public function actionPrepare()
    {
        parent::actionPrepare();

        $this->_new = $this->factoryNews->news->getByAlias($this->_params['alias'], true);

        if ($this->_new === null) {
            return $this->_response->setStatus(HttpResponse::S4_NOT_FOUND);
        }

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

        return null;
    }

    /**
     * Обработчик GET-запросов
     *
     * @return mixed
     */
    public function actionGet()
    {
        $vars = [
            'action' => 'edit',
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
     * Обработчик POST-запросов
     *
     * @return void|HttpResponse
     */
    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'edit':
                return $this->_edit();
        }

        return parent::actionPost();
    }
}
