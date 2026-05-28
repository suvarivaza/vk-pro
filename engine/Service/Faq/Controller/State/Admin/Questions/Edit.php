<?php

namespace Service\Faq;

class Controller_State_Admin_Questions_Edit extends Controller_State_Admin_Questions
{
    public function actionPrepare()
    {
        parent::actionPrepare();

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

        $this->_rubric = $this->factoryFaq->rubrics->getById($this->_params['rubricId']);

        if ($this->_rubric === null) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }

        $this->_question = $this->factoryFaq->questions->getById($this->_params['qId'], true);

        return null;
    }

    /**
     * @return mixed
     */
    public function actionGet()
    {
        $vars = [
            'action' => 'add',
            'rubric' => $this->_rubric,
            'question' => $this->_question,
            'errors' => $this->_errors,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/questions/edit', $vars));
    }

    /**
     * @return \System\HttpResponse|null
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
