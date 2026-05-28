<?php

namespace Service\Posting;

class Controller_State_Client_Add extends Controller_State_Client
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        $this->_group = $this->factoryPosting->groups->getById($this->_request->get['groupId']->int(0));

        if ($this->_group === null || $this->_group->userId != $this->_application->UserID) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }
        $this->_post = $this->factoryPosting->posts->getNew();

        $this->_application->Title->addScripts(
            [
                '/js/plupload/plupload.full.min.js',
                '/js/plupload/uploader.js',
                '/js/posts/edit.min.js',
                '/js/bootstrap/bootstrap-datepicker.min.js',
                '/js/bootstrap/bootstrap-datetimepicker.min.js',
            ]
        );

        $this->_application->Title->addStyles([
            '/css/bootstrap/bootstrap-datepicker.min.css',
            '/css/uploader.css',
            '/css/bootstrap/bootstrap-datetimepicker.min.css',
        ]);

        return null;
    }

    public function actionGet()
    {
        $vars = [
            'action' => 'add',
            'errors' => $this->_errors,
            'post' => $this->_post,
            'uploader' => [
                'url' => $this->_request->server['REQUEST_URI']->string(),
                'uuid' => \Lib_Uuid::getNext(),
            ],
        ];

        return $this->_response->setBody(\STPL::Fetch('client/edit', $vars));
    }

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
