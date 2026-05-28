<?php

namespace Service\Pages;

class Controller_Client_Default extends Controller_Client
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response != null) {
            return $response;
        }

        $this->_params['path'] = trim($this->_params['path'], '/');

        if (isset($this->_application->menu[$this->_params['path']])) {
            $this->_application->menu[$this->_params['path']]['active'] = true;
        }

        return null;
    }

    /**
     * @return \System\HttpResponse
     */
    public function actionGet()
    {
        $pages = new Model_Pages();

        $page = $pages->GetPageByAlias($this->_params['path'], true);

        if ($page === null) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }



        $page->count++;
        $pages->save($page);
//        var_dump("OK"); die;


        if ($page->isNew) {
            $this->_application->menu['news']['active'] = true;
        } elseif ($page->isArticle) {
            $this->_application->menu['articles']['active'] = true;
        } else {
            if (isset($this->_application->menu[$page->alias])) {
                $this->_application->menu[$page->alias]['active'] = true;
            }
        }

        if ($page === null) {
            $response = new \System\HttpResponse();

            return $response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }



        $this->_application->Title->appendBefore($page->title);
        $this->_application->Title->Description = $page->describe;
        $this->_application->Title->Keywords = $page->keywords;

        $this->_application->Title->add('meta', [
            'name' => 'og:title',
            'content' => $page->title,
        ]);
        $this->_application->Title->add('meta', [
            'name' => 'og:description',
            'content' => $page->describe,
        ]);
        $this->_application->Title->add('meta', [
            'name' => 'og:type',
            'content' => 'website',
        ]);

        $this->_application->Title->add('meta', [
            'name' => 'og:image',
            'content' => 'https://vk-pro.top/img/logo.white.135.png',
        ]);
        $this->_application->Title->add('meta', [
            'name' => 'og:image:type',
            'content' => 'image/png',
        ]);
        $this->_application->Title->add('meta', [
            'name' => 'og:image:width',
            'content' => '616',
        ]);
        $this->_application->Title->add('meta', [
            'name' => 'og:image:height',
            'content' => '179',
        ]);

//        $this->_application->Title->addScript('/js/comments.js');
        $this->_application->page = $page->alias;

        $vars = [
            'page' => [
                'title' => $page->title,
                'text' => $page->text,
                'desc' => $page->describe,
                'alias' => $page->alias,
                'isSuccess' => isset($_GET['success']),
                'success' => $this->_request->get['success']->bool(),
                'isArticle' => $page->isArticle,
                'isNew' => $page->isNew,
                'strict' => $page->strict,
                'count' => $page->count,
            ],
        ];

        return $this->_response->setBody(\STPL::Fetch('client/default', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'getPage':
                return $this->_getPage();
        }

        return parent::actionPost();
    }

    private function _getPage()
    {
        $pages = new Model_Pages();

        $page = $pages->GetPageByAlias($this->_params['path'], true);

        if ($page === null) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }

        $page->count++;
        $pages->save($page);

        if ($page->isNew) {
            $this->_application->menu['news']['active'] = true;
        } elseif ($page->isArticle) {
            $this->_application->menu['articles']['active'] = true;
        } else {
            if (isset($this->_application->menu[$page->alias])) {
                $this->_application->menu[$page->alias]['active'] = true;
            }
        }

        if ($page === null) {
            $response = new \System\HttpResponse();

            return $response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }

        $vars = [
            'success' => true,
            'page' => [
                'title' => $page->title,
                'text' => $page->text,
                'alias' => $page->alias,
            ],
        ];

        return $this->_response->setJson($vars);
    }
}
