<?php

namespace Service\News;

use STPL;
use System\HttpResponse;

class Controller_State_Client_Detail extends Controller_State_Client
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
     * @return mixed
     */
    public function actionGet()
    {
        $page = $this->factoryNews->news->getByAlias($this->_params['path'], true);

        if ($page === null) {
            return $this->_response->setStatus(HttpResponse::S4_NOT_FOUND);
        }

        if ($page === null) {
            $response = new HttpResponse();

            return $response->setStatus(HttpResponse::S4_NOT_FOUND);
        }

        $this->_application->Title->appendBefore($page->title);
        $this->_application->Title->Description = $page->desc;
        $this->_application->Title->Keywords = $page->keywords;

        $this->_application->Title->add('meta', [
            'name' => 'og:title',
            'content' => $page->title,
        ]);
        $this->_application->Title->add('meta', [
            'name' => 'og:description',
            'content' => $page->desc,
        ]);
        $this->_application->Title->add('meta', [
            'name' => 'og:type',
            'content' => 'website',
        ]);

        $this->_application->Title->add('meta', [
            'name' => 'og:image',
            'content' => '/img/logo.jpg',
        ]);
        $this->_application->Title->add('meta', [
            'name' => 'og:image:type',
            'content' => 'image/jpeg',
        ]);
        $this->_application->Title->add('meta', [
            'name' => 'og:image:width',
            'content' => '250',
        ]);
        $this->_application->Title->add('meta', [
            'name' => 'og:image:height',
            'content' => '163',
        ]);

        $this->_application->Title->addScript('/js/comments.js');

        $vars = [
            'page' => [
                'title' => $page->title,
                'text' => $page->text,
                'alias' => $page->alias,
            ],
        ];

        return $this->_response->setBody(STPL::Fetch('client/default', $vars));
    }

    public function actionPost()
    {
        return parent::actionPost();
    }
}
