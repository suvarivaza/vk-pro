<?php

namespace Service\Users;

class Controller_Widget_Menu extends Controller_Widget
{
    private $_menu = [
        'general' => [
            'title' => 'Профиль',
            'href' => '/users/general',
            'active' => false,
            'icon' => '/img/icons/32/icon-profile.png',
            'icon_active' => '/img/icons/32/icon-profile-white.png',
        ],
        'tasks' => [
            'title' => 'История заданий',
            'href' => '/users/tasks',
            'active' => false,
            'icon' => '/img/icons/32/icon-tasks.png',
            'icon_active' => '/img/icons/32/icon-tasks-white.png',
        ],
        'buy' => [
            'title' => 'История покупок',
            'href' => '/users/buy',
            'active' => false,
            'icon' => '/img/icons/32/icon-buy.png',
            'icon_active' => '/img/icons/32/icon-buy-white.png',
        ],
        'penalty' => [
            'title' => 'История баланса',
            'href' => '/users/penalty',
            'active' => false,
            'icon' => '/img/icons/32/icon-penalty.png',
            'icon_active' => '/img/icons/32/icon-penalty-white.png',
        ],
        'services' => [
            'title' => 'Приобретенные функции',
            'href' => '/users/services',
            'active' => false,
            'icon' => '/img/icons/32/icon-services.png',
            'icon_active' => '/img/icons/32/icon-services-white.png',
        ],
        'bonus' => [
            'title' => 'Бонусы',
            'href' => '/users/bonus',
            'active' => false,
            'icon' => '/img/icons/32/icon-bonus.png',
            'icon_active' => '/img/icons/32/icon-bonus-white.png',
        ],
        'karma' => [
            'title' => 'Карма',
            'href' => '/users/karma',
            'active' => false,
            'icon' => '/img/icons/32/icon-karma.png',
            'icon_active' => '/img/icons/32/icon-karma-white.png',
        ],
        'referrer' => [
            'title' => 'Партнёрская программа',
            'href' => '/users/referrer',
            'active' => false,
            'icon' => '/img/icons/32/icon-referrer.png',
            'icon_active' => '/img/icons/32/icon-referrer-white.png',
        ],
        'faqfaq' => [
            'title' => 'FAQ',
            'href' => '/faq',
            'active' => false,
            'icon' => '/img/icons/32/icon-help.png',
            'icon_active' => '/img/icons/32/icon-help-white.png',
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

        $factoryFaq = new \Service\Faq\Model_Factory();
        $count = $factoryFaq->questions->getCount($this->_application->UserID);

        $title = 'Техническая поддержка';

        if ($count > 0) {
            $title .= ' (' . $count . ')';
        }
        $list['faq'] = [
            'title' => $title,
            'href' => '/faq/my',
            'active' => true,
            'icon' => '/img/icons/32/icon-help.png',
            'icon_active' => '/img/icons/32/icon-help-white.png',
        ];
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
