<?php

class Lib_Proxy
{

    public static $proxies = [];


    public static function getProxy()
    {

        $proxyUrl = Config::$proxyUrl;

        $i = 0;
        do {
            if ($i > 0) sleep(1);

            $response = file_get_contents($proxyUrl);
            $proxy = json_decode($response);

            if (!empty($proxy->results)) break;
            $i++;
        } while ($i <= 3);

        if (empty($proxy->results)) {
            logMail('Vk-Pro.top Get Proxy Error!', "Не удалось получить прокси по API - {$proxyUrl} Получен ответ: " . $response);
            return false;
        }

        $proxy_list = $proxy->results;
        foreach ($proxy_list as $proxy) {
            $proxies[] = $proxy->ip . ':' . $proxy->port_http . ':' . $proxy->username . ':' . $proxy->password;
        }
        self::$proxies = $proxies;

        return self::$proxies;

    }

}