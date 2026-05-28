<?php

namespace Service\Messages;

class Controller_State_Admin_Default extends Controller_State_Admin
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        $this->_message = $this->factoryMessages->messages->getNew();

        if (isset($this->_application->menu['messages']['menu']['all'])) {
            $this->_application->menu['messages']['menu']['all']['active'] = true;
        }

        return null;
    }

    public function actionGet()
    {
        $query = $this->factoryMessages->messages->query()->sqlCalcFoundRows(true);
        $query->limit(100)->offset(($this->_params['page'] - 1) * 100);

        $it = $query->iterator();

        $list = [];

        foreach ($it as $message) {
            $list[] = $message;
        }

        $pageslink = \Lib_Html::GetNavigationPagesNumber(
            100,
            10,
            $it->getTotal(),
            $this->_params['page'],
            '/admin/messages/@p@',
            2
        );

        $vars = [
            'errors' => $this->_errors,
            'pageslink' => $pageslink,
            'list' => $list,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/list', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'add':
                return $this->_edit();
        }
    }
}
