<?php

namespace Service\Faq;

class Controller_State_Admin_Rubrics_List extends Controller_State_Admin_Rubrics
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        if ($this->_request->get['isDel']->int(0) > 0) {
            $rubric = $this->factoryFaq->rubrics->getById($this->_request->get['isDel']->int(0), true);

            if ($rubric !== null) {
                $this->factoryFaq->rubrics->delete($rubric);
            }

            return $this->_response->setLocation($this->_request->server['REDIRECT_URL']->string());
        }

        if (isset($this->_application->menu['faq']['menu']['list'])) {
            $this->_application->menu['faq']['menu']['list']['active'] = true;
        }

        return null;
    }

    public function actionGet()
    {
        $query = $this->factoryFaq->rubrics->query()->limit(1000);
        $it = $query->iterator();

        $list = [];

        foreach ($it as $rubric) {
            $list[] = $rubric;
        }

        $vars = [
            'list' => $list,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/rubrics/list', $vars));
    }

    public function actionPost()
    {
        return $this->_response->setStatus(\System\HttpResponse::S4_METHOD_NOT_ALLOWED);
    }
}
