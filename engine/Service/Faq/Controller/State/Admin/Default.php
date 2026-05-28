<?php

namespace Service\Faq;

/**
 * Class Controller_State_Admin
 *
 * @package Service\Faq
 */
class Controller_State_Admin_Default extends Controller_State_Admin
{
    public function actionGet()
    {
        return $this->_response->setLocation('/admin/faq/rubrics');
    }

    public function actionPost()
    {
        return $this->_response->setStatus(\System\HttpResponse::S4_METHOD_NOT_ALLOWED);
    }
}
