<?php

namespace Service\Faq;

class Controller_State_Admin_Rubrics_Edit extends Controller_State_Admin_Rubrics
{
    public function actionPrepare()
    {
        parent::actionPrepare();

        $this->_rubric = $this->factoryFaq->rubrics->getById($this->_params['rubricId'], true);

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
            'errors' => $this->_errors,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/rubrics/edit', $vars));
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
