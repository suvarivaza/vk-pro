<?php

namespace Service\Pages;

class Controller_Admin_Edit extends Controller_Admin
{
    public function actionPrepare()
    {
        parent::actionPrepare();

        $this->_page = $this->pages->GetPageByAlias($this->_params['alias'], true);

        if ($this->_page === null) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
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
        $chain = [];

        if ($this->_page->isArticle) {
            $chain[] = [
                'title' => 'Статьи',
                'url' => '/admin/pages/articles/1',
                'bold' => true,
            ];
            $chain[] = [
                'title' => 'Редактирование статьи "' . $this->_page->title . '"',
            ];
        } elseif ($this->_page->isNew) {
            $chain[] = [
                'title' => 'Новости',
                'url' => '/admin/pages/news/1',
                'bold' => true,
            ];
            $chain[] = [
                'title' => 'Редактирование новости "' . $this->_page->title . '"',
            ];
        } else {
            $chain[] = [
                'title' => 'Страницы',
                'url' => '/admin/pages/list/1',
                'bold' => true,
            ];
            $chain[] = [
                'title' => 'Редактирование страницы "' . $this->_page->title . '"',
            ];
        }

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

        return $this->_response->setBody(\STPL::Fetch('admin/edit', $vars));
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
                return $this->_edit();
        }

        return parent::actionPost();
    }
}
