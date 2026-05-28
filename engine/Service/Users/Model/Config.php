<?php

namespace Service\Users;

class Model_Config
{
    public const TYPE_USER = 0;
    public const TYPE_ADMIN = 1;
    public const TYPE_MODERATOR = 2;

    public const BONUS_DAY = 1;
    public const BONUS_WEEK = 2;

    public const BAD_AVATAR = 2;
    public const BAD_AVATAR_COUNT = 4;
    public const BAD_POSTS = 8;
    public const BAD_FOLLOWERS = 16;

    public static $karmaPath = ENGINE_PATH . 'engine/Service/Users/Model/Karma.json';
    public static $bonusPath = ENGINE_PATH . 'engine/Service/Users/Model/Bonus.json';
    public static $referrersPath = ENGINE_PATH . 'engine/Service/Users/Model/Referrers.json';
    public static $requestsStatuses = [
        0 => [
            'title' => 'Заявка на модерации',
        ],
        1 => [
            'title' => 'Заявка принята к исполнению',
        ],
        2 => [
            'title' => 'Заявка выполняется',
        ],
        3 => [
            'title' => 'Заявка выполнена',
        ],
        4 => [
            'title' => 'Заявка отклонена',
        ],
    ];
    public static $types = [
        'likes' => 'Лайки',
        'reposts' => 'Репосты',
        'comments' => 'Комментарии',
        'join' => 'Подписка',
        'friends' => 'Друзья',
        'views' => 'Просмотры',
        'video' => 'Видео',
        'polls' => 'Голосования',
    ];
    public static $days = [
        1 => 'Понедельник',
        2 => 'Вторник',
        3 => 'Среда',
        4 => 'Четверг',
        5 => 'Пятница',
        6 => 'Суббота',
        7 => 'Воскресенье',
    ];
    public static $userData = [
        'sex' => [
            1 => 'жен',
            2 => 'муж',
        ],
    ];

    public static function GetReferrersSettings()
    {
        $json = json_decode(file_get_contents(self::$referrersPath), true);

        if (!is_array($json)) {
            $json = [];
        }

        return $json;
    }

    public static function SetReferrersSettings($settings)
    {
        $json = json_encode($settings, JSON_UNESCAPED_UNICODE);
        file_put_contents(self::$referrersPath, $json);

        return true;
    }

    public static function GetKarmaSettings()
    {
        $json = json_decode(file_get_contents(self::$karmaPath), true);

        if (!is_array($json)) {
            $json = [];
        }

        return $json;
    }

    public static function SetKarmaSettings($settings)
    {
        $json = json_encode($settings, JSON_UNESCAPED_UNICODE);
        file_put_contents(self::$karmaPath, $json);

        return true;
    }

    public static function GetBonusSettings()
    {
        $json = json_decode(file_get_contents(self::$bonusPath), true);

        if (!is_array($json)) {
            $json = [];
        }

        return $json;
    }

    public static function SetBonusSettings($settings)
    {
        $json = json_encode($settings, JSON_UNESCAPED_UNICODE);

        $res = file_put_contents(self::$bonusPath, $json);


        return true;
    }
}
