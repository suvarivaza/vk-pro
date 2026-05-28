<?php

namespace Service\Auto;

class Model_Config
{
    public static $autoStartPath = ENGINE_PATH . 'engine/Service/Auto/Template/controls/start.php';
    public static $autoShortPath = ENGINE_PATH . 'engine/Service/Auto/Template/controls/short.php';
    public static $settings = ENGINE_PATH . 'engine/Service/Auto/Model/Config.json';
    public static $tips = ENGINE_PATH . 'engine/Service/Auto/Model/Tips.json';

    private static $config = null;

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
