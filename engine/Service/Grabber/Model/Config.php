<?php

namespace Service\Grabber;

class Model_Config
{
    public static $domains = ENGINE_PATH . 'engine/Service/Grabber/Model/Domains.json';
    public static $copyrightType = [
        1 => [
            'id' => 1,
            'title' => 'Ссылка на группу(link)',
        ],
        2 => [
            'id' => 2,
            'title' => 'Ссылка на группу(title)',
        ],
        3 => [
            'id' => 3,
            'title' => 'Ссылка на автора(link)',
        ],
        4 => [
            'id' => 4,
            'title' => 'Ссылка на автора(title)',
        ],
        5 => [
            'id' => 5,
            'title' => 'Ссылка на пост(link)',
        ],
        6 => [
            'id' => 6,
            'title' => 'Название группы-источника',
        ],
        7 => [
            'id' => 7,
            'title' => 'Имя автора',
        ],
    ];
    public static $copyrightPosition = [
        1 => [
            'id' => 1,
            'title' => 'В конце текста',
        ],
        2 => [
            'id' => 2,
            'title' => 'В начале текста',
        ],
    ];

    public static function getDomains()
    {
        return json_decode(file_get_contents(self::$domains), true);
    }

    public static function setDomains($domains)
    {
        file_put_contents(self::$domains, json_encode($domains, JSON_UNESCAPED_UNICODE));
    }
}
