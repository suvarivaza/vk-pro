<?php

namespace Service\Pages;

class Controller_Admin_Add extends Controller_Admin
{
    public function actionPrepare()
    {
        parent::actionPrepare();

        $this->_page = $this->pages->getNew();
        $this->_page->isArticle = $this->_request->get['isArticle']->bool();
        $this->_page->isNew = $this->_request->get['isNew']->bool();

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
     * @return mixed
     */
    public function actionGet()
    {
        $chain = [];

        if ($this->_page->isArticle) {
            $chain[] = [
                'title' => 'Статьи',
                'url' => '/admin/pages/articles/1',
                'bold' => true,
            ];
            $chain[] = [
                'title' => 'Добавить статью',
            ];
        } elseif ($this->_page->isNew) {
            $chain[] = [
                'title' => 'Новости',
                'url' => '/admin/pages/news/1',
                'bold' => true,
            ];
            $chain[] = [
                'title' => 'Добавление новости',
            ];
        }

        $vars = [
            'action' => 'add',
            'page' => $this->_page,
            'chain' => $chain,
            'uploader' => [
                'url' => $this->_request->server['REQUEST_URI']->string(),
                'uuid' => \Lib_Uuid::getNext(),
            ],
            'errors' => $this->_errors,
        ];

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

        return $this->_response->setBody(\STPL::Fetch('admin/edit', $vars));
    }

    /**
     * @return void|\System\HttpResponse
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
