<?php

namespace Service\Html;

use STPL;
use System\HttpResponse;
use System\Service_Controller_State;

class Controller_Default extends Service_Controller_State
{
    public function actionGet()
    {
        //return $this->_response->setStatus(\System\HttpResponse::S4_BAD_REQUEST);

        $page = $this->_request->get['page']->string('');

        if (!$page) {
            return $this->_response->setStatus(HttpResponse::S4_NOT_FOUND);
        }

        if (!STPL::IsTemplate($page)) {
            return $this->_response->setStatus(HttpResponse::S4_NOT_FOUND);
        }

        return $this->_response->setBody(STPL::Fetch($page));
    }

    public function actionPost()
    {
        $page = $this->_request->post['page']->string('');

        if (!$page) {
            return $this->_response->setStatus(HttpResponse::S4_NOT_FOUND);
        }

        if (!STPL::IsTemplate($page)) {
            return $this->_response->setStatus(HttpResponse::S4_NOT_FOUND);
        }

        return $this->_response->setJson(['success' => true, 'html' => STPL::Fetch($page)]);
    }
}
