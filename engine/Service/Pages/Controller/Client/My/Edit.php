<?php

namespace Service\Pages;

class Controller_Client_My_Edit extends Controller_Admin
{
    public function actionPrepare()
    {
        parent::actionPrepare();

        if (!$this->_application->User->visible) {
            return $this->_response->setStatus(\System\HttpResponse::S4_FORBIDDEN);
        }

        $this->_page = $this->pages->GetPageByAlias($this->_params['alias'], true);

        if ($this->_page === null) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }

        if ($this->_page->userId != $this->_application->UserID) {
            return $this->_response->setStatus(\System\HttpResponse::S4_FORBIDDEN);
        }

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

        return null;
    }

    /**
     * Обработчик GET-запросов
     *
     * @return mixed
     */
    public function actionGet()
    {
        $chain = [];

        $chain[] = [
            'title' => 'Новости',
            'url' => '/my/news/1',
            'bold' => true,
        ];
        $chain[] = [
            'title' => 'Редактирование новости "' . $this->_page->title . '"',
        ];

        $vars = [
            'action' => 'edit',
            'page' => $this->_page,
            'chain' => $chain,
            'uploader' => [
                'url' => $this->_request->server['REQUEST_URI']->string(),
                'uuid' => \Lib_Uuid::getNext(),
            ],
            'errors' => $this->_errors,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/my/edit', $vars));
    }

    /**
     * Обработчик POST-запросов
     *
     * @return void|\System\HttpResponse
     */
    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'edit':
                return $this->_edit(true, '/my/news/1');
        }

        return parent::actionPost();
    }
}
