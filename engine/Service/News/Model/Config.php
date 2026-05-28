<?php

namespace Service\News;

class Model_Config
{
    public static $settingsPath = ENGINE_PATH . 'engine/Service/Users/Model/Karma.json';

    public static function GetSettings()
    {
        $json = json_decode(file_get_contents(self::$settingsPath), true);

        if (!is_array($json)) {
            $json = [];
        }

        return $json;
    }

    public static function SetSettings($settings)
    {
        $json = json_encode($settings, JSON_UNESCAPED_UNICODE);
        file_put_contents(self::$settingsPath, $json);

        return true;
    }
}
