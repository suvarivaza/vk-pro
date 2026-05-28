<?php

namespace Service\Orders;

class Model_Config
{
    public const YANDEX_CODE_OK = '0';
    public const YANDEX_CODE_ERR = '1';
    public const YANDEX_CODE_NO_ORDER = '100';
    public const YANDEX_CODE_PARAMS = '200';

    public static $settings = ENGINE_PATH . 'engine/Service/Orders/Model/Config.json';

    public static function getSettings()
    {
        $json = json_decode(file_get_contents(self::$settings), true);

        return $json;
    }

    public static function setSettings($settings)
    {
        file_put_contents(self::$settings, json_encode($settings, JSON_UNESCAPED_UNICODE));

        return true;
    }
}
