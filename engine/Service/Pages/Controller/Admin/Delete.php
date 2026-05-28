<?php

namespace Service\Pages;

class Controller_Admin_Delete extends Controller_Admin
{
    public function actionPrepare()
    {
        parent::actionPrepare();

        $this->_page = $this->pages->GetPageByAlias($this->_params['alias'], true);

        if ($this->_page === null) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
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

        return $this->_response->setBody(\STPL::Fetch('admin/delete', $vars));
    }

    /**
     * @return void|\System\HttpResponse
     */
    public function actionPost()
    {
        $this->pages->delete($this->_page->pageId);

        return $this->_response->setLocation($this->_request->get['backUrl']->string());
    }
}
