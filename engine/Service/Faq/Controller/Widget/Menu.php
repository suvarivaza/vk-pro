<?php

namespace Service\Faq;

class Controller_Widget_Menu extends Controller_Widget
{
    private $_menu = [
        'general' => [
            'title' => 'Мои вопросы',
            'href' => '/faq/my',
            'active' => false,
            'icon' => '/img/icons/32/icon-help.png',
            'icon_active' => '/img/icons/32/icon-help.png',
        ],
    ];

    /**
     * Обработчик запросов.
     *
     * @return \System\HttpResponse|null
     */
    public function actionGet()
    {
        $list = $this->_menu;
        $show = true;

        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setBody('');
        }

        $vars = [
            'title' => 'Личный<br />кабинет',
            'show' => $show,
            'list' => $list,
            'page' => $this->_application->userPage,
        ];

        $old_paths = \STPL::PathRegister(ENGINE_PATH . 'engine/Service/users/Template/');
        $html = \STPL::Fetch('widget/menu', $vars);
        \STPL::RestorePaths($old_paths);

        return $this->_response->setBody($html);
    }
}
