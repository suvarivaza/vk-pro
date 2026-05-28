<?php

namespace Service\News;

use STPL;
use System\HttpResponse;

class Controller_State_Admin_Delete extends Controller_State_Admin
{
    public function actionPrepare()
    {
        parent::actionPrepare();

        $this->_page = $this->pages->GetPageByAlias($this->_params['alias'], true);

        if ($this->_page === null) {
            return $this->_response->setStatus(HttpResponse::S4_NOT_FOUND);
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function actionGet()
    {
        $vars = [
            'page' => $this->_page,
        ];

        return $this->_response->setBody(STPL::Fetch('admin/delete', $vars));
    }

    /**
     * @return void|HttpResponse
     */
    public function actionPost()
    {
        $this->pages->delete($this->_page->pageId);

        return $this->_response->setLocation($this->_request->get['backUrl']->string());
    }
}
