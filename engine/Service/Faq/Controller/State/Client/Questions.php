<?php

namespace Service\Faq;

class Controller_State_Client_Questions extends Controller_State_Client
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        if ($this->_request->get['isDel']->int(0) > 0) {
            $question = $this->factoryFaq->questions->getById($this->_request->get['isDel']->int(0), true);

            if ($question !== null) {
                $this->factoryFaq->questions->delete($question);
            }

            return $this->_response->setLocation($this->_request->server['REDIRECT_URL']->string());
        }
    }

    public function actionGet()
    {
        $rubric = $this->factoryFaq->rubrics->getById($this->_params['rubricId']);

        if ($rubric === null) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }

        $query = $this->factoryFaq->questions->query()->limit(1000);
        $query->filter->fieldValue('rubricId', '=', $rubric->rubricId);
        $query->filter->fieldValue('isVisible', '&', Model_Config::IS_VISIBLE);
        $it = $query->iterator();

        $list = [];

        foreach ($it as $question) {
            $list[] = $question;
        }

        $vars = [
            'rubric' => $rubric,
            'list' => $list,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/questions/list', $vars));
    }

    public function actionPost()
    {
        return $this->_response->setStatus(\System\HttpResponse::S4_METHOD_NOT_ALLOWED);
    }
}
