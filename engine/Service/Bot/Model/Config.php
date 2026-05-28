<?php

namespace Service\Bot;

class Model_Config
{
    public static $settings = ENGINE_PATH . 'engine/Service/Bot/Model/Config.json';
    public static $tips = ENGINE_PATH . 'engine/Service/Bot/Model/Tips.json';

    private static $config = null;

    public static $ids = [2, 4, 8, 16, 32, 64];

    public static $botTypes = [
        2 => 'likes',
        4 => 'reposts',
        8 => 'comments',
        16 => 'join',
        32 => 'friends',
        64 => 'polls',
        128 => 'views',
        256 => 'video'
    ];

    public static function loadConfig()
    {
        self::$config = json_decode(file_get_contents(self::$settings), true);
    }

    public static function getConfig()
    {
        if (self::$config === null) {
            self::$config = json_decode(file_get_contents(self::$settings), true);
        }

        if (!is_array(self::$config)) {
            self::$config = [];
        }

        return self::$config;
    }

    public static function saveConfig($config)
    {
        self::$config = $config;
        file_put_contents(self::$settings, json_encode($config, JSON_UNESCAPED_UNICODE));

        return true;
    }
}
