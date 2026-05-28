<?php

namespace Service\Users;

class Controller_State_Client_Penalty_Penalty extends Controller_State_Client
{
    public function actionPrepare()
    {
        $this->_application->userPage = 'penalty';
        $this->_application->Title->Title = 'Штрафы и компенсации';

        $this->_application->Title->add('link', [
            'rel' => 'icon',
            'href' => '/img/icons/32/icon-penalty.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->add('link', [
            'rel' => 'shortcut icon',
            'href' => '/img/icons/32/icon-penalty.png',
            'type' => 'image/png',
        ]);

        $reponse = parent::actionPrepare();

        if ($reponse !== null) {
            return $reponse;
        }

        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setLocation('/users/login');
        }

        return null;
    }

    public function actionGet()
    {
        $page = $this->_params['page'];

        $list = $this->factoryUsers->users->balance->getByUserIdIsPenalty($this->_application->UserID, true);
        $total = count($list);
        usort($list, [$this, '_sort_karma']);
        $list = array_slice($list, ($page - 1) * $this->_limit, $this->_limit);

        $pageslink = \Lib_Html::GetNavigationPagesNumber(
            $this->_limit,
            4,
            $total,
            $page,
            '/users/penalty/penalty/@p@',
            1
        );

        $vars = [
            'total' => $total,
            'user' => $this->_application->User,
            'list' => $list,
            'pageslink' => $pageslink,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/penalty/penalty', $vars));
    }

    public function actionPost()
    {
    }

    protected function _sort_karma($a, $b)
    {
        return $a->dateCreate < $b->dateCreate;
    }
}
