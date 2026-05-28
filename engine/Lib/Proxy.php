<?php


class Lib_Proxy
{

    private static $proxyUrl = 'https://panel.proxyline.net/api/proxies/?api_key=iqu34vc62pow9ceobfflrwixlyjtffeb1itcvye6&status=active&country=ru';

    // get proxy by api
    // if we have proxies in session - return from session
    private static function getProxies()
    {

        if (!empty($_SESSION['proxies'])) return $_SESSION['proxies'];

        $proxyUrl = self::$proxyUrl;

        //do 3 tries to ret proxies
        $i = 0;
        do {
            if ($i > 0) sleep(1);
            $i++;

            $response = file_get_contents($proxyUrl);
            $proxy = json_decode($response);

            if (!empty($proxy->results)) break;
        } while ($i < 3);

        if (empty($proxy->results)) {
            $error = "Filed to get proxy by API - {$proxyUrl} Response: " . $response;
            self::log($error);
            return false;
        }

        self::setTempProxies($proxy->results);

        return $proxy->results;

    }

    private static function setTempProxies($proxies){
        $_SESSION['proxies'] = $proxies;
    }



    private static function deleteCurrentTempProxy(){
        $tempProxies = self::getTempProxies();
        $currentTempProxy = self::getCurrentTempProxy();
        //delete current proxy
        foreach ($tempProxies as $k => $proxy) {
            if ($proxy->ip == $currentTempProxy->ip) {
                unset($_SESSION['proxies'][$k]);
                unset($_SESSION['tmpProxy']);
            }
        }
    }


    private static function getRandomTempProxy()
    {
        if(!empty($_SESSION['proxies'])) $proxies = $_SESSION['proxies'];
        else $proxies = self::getProxies();

        $k = array_rand($proxies);
        $randomProxy = $proxies[$k];
        return $randomProxy;
    }

    private static function getTempProxies()
    {
        if (!empty($_SESSION['proxies'])) return $_SESSION['proxies'];

        $proxies = self::getProxies();
        $_SESSION['proxies'] = $proxies;
        return $proxies;
    }

    public static function getCurrentTempProxy()
    {
        if (!empty($_SESSION['tmpProxy'])) return $_SESSION['tmpProxy'];

        $proxy = self::getRandomTempProxy();
        $_SESSION['tmpProxy'] = $proxy;
        return $proxy;
    }


    public static function getNewTempProxy()
    {
        self::deleteCurrentTempProxy();
        return self::getCurrentTempProxy();
    }


    private static function log($error)
    {
        mail('42-36-42@mail.ru', 'Vk-pro.top Get Proxy Error!', $error);
    }

}