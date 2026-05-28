<?php

namespace Service\Faq;

use STPL;
use System\HttpResponse;

class Controller_State_Admin_Questions_List extends Controller_State_Admin_Rubrics
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

        $query = $this->factoryFaq->questions->query()->limit(1000);

        if ($rubric !== null) {
            $query->filter->fieldValue('rubricId', '=', $rubric->rubricId);
            $query->filter->fieldValue('isVisible', '&', Model_Config::IS_VISIBLE);
        } else {
            $rubric = $this->factoryFaq->rubrics->getNew();
        }

        if ($this instanceof Controller_State_Admin_Questions_List_User) {
            if (isset($this->_application->menu['faq']['menu']['new'])) {
                $this->_application->menu['faq']['menu']['new']['active'] = true;
            }

            $query->filter->fieldValue('isVisible', '=', Model_Config::IS_FROM_USER);
        }

        $it = $query->iterator();

        $list = [];

        foreach ($it as $question) {
            $list[] = $question;
        }

        $vars = [
            'user' => $this instanceof Controller_State_Admin_Questions_List_User,
            'rubric' => $rubric,
            'list' => $list,
        ];

        return $this->_response->setBody(STPL::Fetch('admin/questions/list', $vars));
    }

    public function actionPost()
    {
        return $this->_response->setStatus(HttpResponse::S4_METHOD_NOT_ALLOWED);
    }
}
