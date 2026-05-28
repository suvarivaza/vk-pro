<?php

namespace Service\Users;

class Controller_State_Admin_Referrers_Out extends Controller_State_Admin
{
    public function actionPrepare()
    {
        $this->_application->userPage = 'referrer';

        if (isset($this->_application->menu['users']['menu']['requests'])) {
            $this->_application->menu['users']['menu']['requests']['active'] = true;
        }

        return parent::actionPrepare();
    }

    public function actionGet()
    {
        $page = $this->_request->get['p']->int(0);
        $query = $this->factoryUsers->users->requests->query()->limit($this->_limit)->offset(($page - 1) * $this->_limit)->sort('dateCreate',
            'DESC')->sqlCalcFoundRows(true);
        $it = $query->iterator();
        $total = $it->getTotal();

        $pageslink = \Lib_Html::GetNavigationPagesNumber(
            $this->_limit,
            4,
            $it->getTotal(),
            $page,
            '/users/admin/referrers/out?p=@p@',
            1
        );

        $vars = [
            'status' => Model_Config::$requestsStatuses,
            'user' => $this->_application->User,
            'total' => $total,
            'list' => $it,
            'pageslink' => $pageslink,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/referrers/out', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'outPrepare':
                return $this->_outPrepare();
            case 'out':
                return $this->_out();
        }
    }

    protected function _outPrepare()
    {
        if (!$this->_application->User->qiwi) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => 'Перед подачей заявки необходимо указать кошелек qiwi.<a href="/users/general">Указать</a>',
            ]);
        }

        $vars = [
            'user' => $this->_application->User,
        ];

        return $this->_response->setJson([
            'success' => true,
            'html' => \STPL::Fetch('client/referrer/out_form', $vars),
        ]);
    }

    protected function _out()
    {
        $balanceRef = $this->_request->post['balanceRef']->dec(0.0);

        if (!$balanceRef) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Укажите сумму']);
        }

        if ($balanceRef > $this->_application->User->balanceRef) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => 'Указанная сумма превышает Ваш баланс',
            ]);
        }

        $request = $this->factoryUsers->users->requests->getNew();
        $request->userId = $this->_application->User->userId;
        $request->dateCreate = time();
        $request->status = 0;
        $request->balanceRef = $balanceRef;

        if ($this->factoryUsers->users->requests->save($request)) {
            $user = $this->_application->User;
            $user->makeShadow();
            $user->balanceRef -= $request->balanceRef;
            $this->factoryUsers->users->save($user);

            return $this->_response->setJson(['success' => true]);
        }
    }
}
