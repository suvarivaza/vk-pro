<?php

namespace Service\Messages;

class Model_Config
{
    public const TYPE_SYSTEM = 1;
    public const TYPE_VIP = 2;
    public const TYPE_ADMIN = 3;
    public const TYPE_USER = 4;

    public const ICON_PROFILE = 'profile';
    public const ICON_BALANCE = 'bonus';
    public const ICON_KARMA = 'karma';
    public const ICON_TASK = 'tasks';

    public static $path = ENGINE_PATH . 'engine/Service/Messages/Model/Config.json';
    public static $icons = [
        'vkpro' => '/img/icons/32/icon-profile-white.png',
        'bonus' => '/img/icons/32/icon-bonus-white.png',
        'karma' => '/img/icons/32/icon-karma-white.png',
        'tasks' => '/img/icons/32/icon-tasks-white.png',
        'auto' => '/img/icons/32/icon-auto-white.png',
        'posting' => '/img/icons/32/icon-post-white.png',
        'grabber' => '/img/icons/32/icon-grabber-white.png',
        'special' => '/img/icons/32/icon-special-white.png',
    ];
    public static $types = [
        'tasks' => [
            'title' => 'Задания',
            'types' => [
                'add' => [
                    'title' => 'Создание задания',
                    'text' => '',
                ],
                'done' => [
                    'title' => 'Завершение задания',
                    'text' => '',
                ],
            ],
        ],
        'auto' => [
            'title' => 'Автоведение',
            'types' => [
                'add' => [
                    'title' => 'Создание задания по шаблону',
                    'text' => '',
                ],
                'done' => [
                    'title' => 'Задание выполнено',
                    'text' => '',
                ],
                'template_play' => [
                    'title' => 'Шаблон автоведения запущен',
                    'text' => '',
                ],
                'template_stop' => [
                    'title' => 'Шаблон автоведения остановлен',
                    'text' => '',
                ],
                'balance' => [
                    'title' => 'Закончился баланс',
                ],
            ],
        ],
        'posting' => [
            'title' => 'Автопостинг',
            'types' => [
                'publish' => [
                    'title' => 'Публикация поста',
                    'text' => '',
                ],
            ],
        ],
        'grabber' => [
            'title' => 'Граббер',
            'types' => [
                'publish' => [
                    'title' => 'Публикация поста',
                    'text' => '',
                ],
            ],
        ],
        'special' => [
            'title' => 'Спецзадания',
            'types' => [
                'add' => [
                    'title' => 'Создание задания',
                    'text' => '',
                ],
                'done' => [
                    'title' => 'Завершение задания',
                    'text' => '',
                ],
            ],
        ],
        'vkpro' => [
            'title' => 'Системные сообщения',
            'types' => [
                'register' => [
                    'title' => 'Регистрация',
                    'text' => '',
                ],
                'day_first' => [
                    'title' => 'Первый вход за день',
                    'text' => '',
                ],
            ],
        ],
        'bonus' => [
            'title' => 'Сообщения бонусов',
            'types' => [
                'regiter' => [
                    'title' => 'Бонус при регистрации',
                    'text' => '',
                ],
                'day_task' => [
                    'title' => 'Бонус за ежедневное задание',
                    'text' => '',
                ],
                'day_info' => [
                    'title' => 'Ежедневный бонус(информация)',
                    'text' => '',
                ],
                'day' => [
                    'title' => 'Ежедневный бонус',
                    'text' => '',
                ],
                'week_info' => [
                    'title' => 'Еженедельный бонус(информация)',
                    'text' => '',
                ],
                'week' => [
                    'title' => 'Еженедельный бонус',
                    'text' => '',
                ],
                'first' => [
                    'title' => 'Первая покупка',
                    'text' => '',
                ],
            ],
        ],
        'karma' => [
            'title' => 'Сообщения кармы',
            'types' => [
                '50' => [
                    'title' => '50% кармы',
                    'text' => '',
                ],
                '75' => [
                    'title' => '75% кармы',
                    'text' => '',
                ],
                'minus' => [
                    'title' => 'Карма в минус',
                    'text' => '',
                ],
                'clear' => [
                    'title' => 'Очистка кармы',
                    'text' => '',
                ],
                'penalty' => [
                    'title' => 'Штраф за задания',
                    'text' => '',
                ],
                'ban' => [
                    'title' => 'Бан в ВК',
                    'text' => '',
                ],
            ],
        ],
    ];

    public static function GetConfig()
    {
        $json = json_decode(file_get_contents(self::$path), true);

        if (!is_array($json)) {
            $json = [];
        }

        return $json;
    }

    public static function SetConfig($settings)
    {
        $json = json_encode($settings, JSON_UNESCAPED_UNICODE);
        file_put_contents(self::$path, $json);

        return true;
    }
}
