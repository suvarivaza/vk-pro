<?php

namespace Service\Pages;

class Controller_Client_My_Delete extends Controller_Admin
{
    public function actionPrepare()
    {
        parent::actionPrepare();

        if (!$this->_application->User->visible) {
            return $this->_response->setStatus(\System\HttpResponse::S4_FORBIDDEN);
        }

        $this->_page = $this->pages->GetPageByAlias($this->_params['alias'], true);

        if ($this->_page === null) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }

        if ($this->_page->userId != $this->_application->UserID) {
            return $this->_response->setStatus(\System\HttpResponse::S4_FORBIDDEN);
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

        return $this->_response->setBody(\STPL::Fetch('client/my/delete', $vars));
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
