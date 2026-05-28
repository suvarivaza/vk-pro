<?php

namespace Service\Admin;

class Model_Config
{
    public static $menu = [
        'general' => [
            'title' => 'Основная панель',
            'href' => '/admin',
            'active' => false,
            'icon' => 'fa-dashboard',
            'menu' => [],
        ],
        'users' => [
            'title' => 'Пользователи',
            'href' => '/admin/users',
            'active' => false,
            'icon' => 'fa-users',
            'menu' => [
                'default' => [
                    'title' => 'Статистика',
                    'href' => '/admin/users',
                    'active' => false,
                ],
                'all' => [
                    'title' => 'Все пользователи',
                    'href' => '/admin/users/list/1',
                    'active' => false,
                ],
                'admins' => [
                    'title' => 'Администраторы',
                    'href' => '/admin/users/list/admins/1',
                    'active' => false,
                ],
                'access_token' => [
                    'title' => 'Пользователи с токенами',
                    'href' => '/admin/users/list/access_token/1',
                    'active' => false,
                ],
                'token_require' => [
                    'title' => 'token_require',
                    'href' => '/admin/users/list/token_require/1',
                    'active' => false,
                ],
                'bad' => [
                    'title' => 'Не прошли проверку',
                    'href' => '/admin/users/list/bad/1',
                    'active' => false,
                ],
                'karma' => [
                    'title' => 'Карма',
                    'href' => '/admin/users/karma',
                    'active' => false,
                ],
                'penalty' => [
                    'title' => 'Штрафы',
                    'href' => '/admin/users/penalty',
                    'active' => false,
                ],
                'bonus' => [
                    'title' => 'Бонусы',
                    'href' => '/admin/users/bonus',
                    'active' => false,
                ],
                'referrers' => [
                    'title' => 'Рефералы',
                    'href' => '/admin/users/referrers',
                    'active' => false,
                ],
                'requests' => [
                    'title' => 'Заявки',
                    'href' => '/admin/users/referrers/out',
                    'active' => false,
                ],
                'settings' => [
                    'title' => 'Настройки вывода средств',
                    'href' => '/admin/users/referrers/out/settings',
                    'active' => false,
                ],
            ],
        ],
        'tasks' => [
            'title' => 'Задания',
            'href' => '/admin/tasks',
            'active' => false,
            'icon' => 'fa-hospital-o',
            'menu' => [
                'stat' => [
                    'title' => 'Статистика',
                    'href' => '/admin/tasks/stat',
                    'active' => false,
                ],
                'bot' => [
                    'title' => 'Автобот',
                    'href' => '/admin/bot',
                    'active' => false,
                ],
                'all' => [
                    'title' => 'Все задания',
                    'href' => '/admin/tasks',
                    'active' => false,
                ],
                'special' => [
                    'title' => 'Спецзадания',
                    'href' => '/admin/tasks/list/special',
                    'active' => false,
                ],
                'price' => [
                    'title' => 'Цены на задания',
                    'href' => '/admin/tasks/prices',
                    'active' => false,
                ],
                'targeting' => [
                    'title' => 'Наценка на таргетинг',
                    'href' => '/admin/tasks/prices/percents',
                    'active' => false,
                ],
                'abuse' => [
                    'title' => 'Жалобы',
                    'href' => '/admin/tasks/abuse',
                    'active' => false,
                ],
                'limits' => [
                    'title' => 'Лимиты выполнения заданий',
                    'href' => '/admin/tasks/limits',
                    'active' => false,
                ],
                'cities' => [
                    'title' => 'Города',
                    'href' => '/admin/tasks/cities',
                    'active' => false,
                ],
                'black' => [
                    'title' => 'Черный список',
                    'href' => '/admin/tasks/blacklist',
                    'active' => false,
                ],
            ],
        ],
        'tips' => [
            'title' => 'Подсказки',
            'href' => '/admin/tasks/tips/likes',
            'active' => false,
            'icon' => ' fa-info-circle',
            'menu' => [
                'tips' => [
                    'title' => 'Подсказки',
                    'href' => '/admin/tasks/tips/likes',
                    'active' => false,
                ],
                'special' => [
                    'title' => 'Подсказки в спецзаданиях',
                    'href' => '/admin/tasks/tips/special/likes',
                    'active' => false,
                ],
                'auto' => [
                    'title' => 'Автоведение',
                    'href' => '/admin/auto/tips/fields',
                    'active' => false,
                ],
            ],
        ],

        'orders' => [
            'title' => 'Покупки',
            'href' => '/admin/orders',
            'active' => false,
            'icon' => ' fa-credit-card',
            'menu' => [
                'stat' => [
                    'title' => 'Статистика',
                    'href' => '/admin/orders/stat',
                    'active' => false,
                ],
                'stat_services' => [
                    'title' => 'Сервисы',
                    'href' => '/admin/orders/services',
                    'active' => false,
                ],
                'list' => [
                    'title' => 'Список покупок',
                    'href' => '/admin/orders/list/1',
                    'active' => false,
                ],
                'add' => [
                    'title' => 'Добавить пакет',
                    'href' => '/admin/orders/pack/add',
                    'active' => false,
                ],
                'default' => [
                    'title' => 'Список пакетов',
                    'href' => '/admin/orders',
                    'active' => false,
                ],
                'services' => [
                    'title' => 'Цены на сервисы',
                    'href' => '/admin/orders/settings',
                    'active' => false,
                ],
            ],
        ],
        'news' => [
            'title' => 'Новости',
            'href' => '/admin/news/list/1',
            'active' => false,
            'icon' => ' fa-newspaper-o',
            'menu' => [
                'list' => [
                    'title' => 'Новости',
                    'href' => '/admin/news/list/1',
                    'active' => false,
                ],
                'add' => [
                    'title' => 'Добавить новость',
                    'href' => '/admin/news/add',
                    'active' => false,
                ],
                'settings' => [
                    'title' => 'Настройки',
                    'href' => '/admin/news/settings',
                    'active' => false,
                ],
            ],
        ],
        'messages' => [
            'title' => 'Сообщения',
            'href' => '/admin/messages/',
            'active' => false,
            'icon' => ' fa-commenting-o',
            'menu' => [
                'all' => [
                    'title' => 'Сообщения',
                    'href' => '/admin/messages/',
                    'active' => false,
                ],
                'system' => [
                    'title' => 'Системные',
                    'href' => '/admin/messages/config',
                    'active' => false,
                ],
            ],
        ],
        'faq' => [
            'title' => 'Вопрос-ответ',
            'href' => '/admin/faq',
            'active' => false,
            'icon' => ' fa-question-circle',
            'menu' => [
                'list' => [
                    'title' => 'Вопрос-ответ',
                    'href' => '/admin/faq',
                    'active' => false,
                ],
                'new' => [
                    'title' => 'Вопросы для ответа',
                    'href' => '/admin/faq/my',
                    'active' => false,
                ],
                'questions' => [
                    'title' => 'Все вопросы',
                    'href' => '/admin/faq/list/1',
                    'active' => false,
                ],
            ],
        ],
        'pages' => [
            'title' => 'Страницы',
            'href' => '/admin/pages/',
            'active' => false,
            'icon' => ' fa-file-text-o',
            'menu' => [],
        ],
        'system' => [
            'title' => 'Настройки',
            'href' => '/admin/system/settings',
            'active' => false,
            'icon' => ' fa-file-text-o',
            'menu' => [
                'settings' => [
                    'title' => 'Настройки',
                    'href' => '/admin/system/settings',
                    'active' => false,
                ],
                'logs' => [
                    'title' => 'Логи',
                    'href' => '/admin/logs/list/1',
                    'active' => false,
                ],
            ],
        ],
        'logs' => [
            'title' => 'Logs',
            'href' => '/admin/logs',
            'active' => false,
            'icon' => ' fa-file-text-o',
            'menu' => [
                'bot-bot' => [
                    'title' => 'Bot Bot',
                    'href' => '/admin/system/logs/bot-bot',
                    'active' => false,
                ],
                'grabber-run' => [
                    'title' => 'Grabber Run',
                    'href' => '/admin/system/logs/grabber-run',
                    'active' => false,
                ],
                'grabber-grab' => [
                    'title' => 'Grabber Grab',
                    'href' => '/admin/system/logs/grabber-grab',
                    'active' => false,
                ],'auto-groups-run' => [
                    'title' => 'Auto Groups Run',
                    'href' => '/admin/system/logs/auto-groups-run',
                    'active' => false,
                ],
                'users-email-send' => [
                    'title' => 'Users Email Send',
                    'href' => '/admin/system/logs/users-email-send',
                    'active' => false,
                ],
                'posting-run' => [
                    'title' => 'Posting Run',
                    'href' => '/admin/system/logs/posting-run',
                    'active' => false,
                ],
                'tasks-check-5min' => [
                    'title' => 'Tasks Check 5Min',
                    'href' => '/admin/system/logs/tasks-check-5min',
                    'active' => false,
                ],
                'tasks-check-hour' => [
                    'title' => 'Tasks Check Hour',
                    'href' => '/admin/system/logs/tasks-check-hour',
                    'active' => false,
                ],
                'tasks-check-day' => [
                    'title' => 'Tasks Check Day',
                    'href' => '/admin/system/logs/tasks-check-day',
                    'active' => false,
                ],
                'tasks-check-month' => [
                    'title' => 'Tasks Check Month',
                    'href' => '/admin/system/logs/tasks-check-month',
                    'active' => false,
                ],
                'users-cities' => [
                    'title' => 'Users Cities',
                    'href' => '/admin/system/logs/users-cities',
                    'active' => false,
                ],
                'users-bonus-day' => [
                    'title' => 'Users Bonus Day',
                    'href' => '/admin/system/logs/users-bonus-day',
                    'active' => false,
                ],
            ],
        ],
    ];
}
