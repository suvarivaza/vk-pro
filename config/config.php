<?php

//Регистрируем нужные константы
define( 'VAR_PATH', '/var/www/www-root/data/www/vk-pro.top/' );

define( 'ENGINE_PATH', VAR_PATH );
define( 'CONFIG_PATH', ENGINE_PATH . 'config/' );

//подключаем нужные файлы
require_once(CONFIG_PATH . 'databases/abstract.php');
require_once(CONFIG_PATH . 'databases/main.php');
require_once(CONFIG_PATH . 'databases/logs.php');
include ENGINE_PATH . "engine/Lib/PHPMailer/PHPMailerAutoload.php";

define( 'IMAGES_PATH', ENGINE_PATH . 'img/' );
define( 'STYLES_PATH', ENGINE_PATH . 'css/' );
define( 'SCRIPTS_PATH', ENGINE_PATH . 'js/' );
define( 'LOG_PATH', VAR_PATH . 'logs/' );
define( 'DOMAIN', 'vk-pro.top' );
define( 'DEFAULT_TABLE_CLASS', 'table table-hover table-condensed table-striped' );

//Standalone-приложение 7909307 созданно на аккаунте Дарья Никитина https://vk.com/apps?act=manage
define('VK_ID'        , '7909307');
define( 'VK_SECURE'   , 'KPrAhqegdz5d5a5Na8IT');
define( 'VK_REDIRECT_URL' , 'http://vk-pro.top/users/social/callback' );

// API - https://vk.com/dev/implicit_flow_user
define( 'VK_TOKEN_URL' , 'https://oauth.vk.com/authorize?client_id=' . VK_ID .'&redirect_uri=https://oauth.vk.com/blank.html&display=page&scope=notify,friends,photos,audio,video,pages,wall,offline,docs,groups,stats,email&response_type=token&v=5.67' );


//Для Юкассы
define('ShopIdYooKassa', '777141');
define('ApiYooKassa', 'live_UD-tFc1TchbvncDESeV8rF3AFQEYs45ST9kG3TwGphg');
//define('scid', '2021978');

//старые данные Для Юкассы
//define('shopId', '152224');
//define('scid', '145151');
//define('shopPassword', '2dP74W44');
//define('shopUrl', 'https://yoomoney.ru/eshop.xml');

//Для старого апи яндекс кассы: (не используется)
//checkUrl - https://vk-pro.top/orders/yandex/check
//avisoUrl - https://vk-pro.top/orders/yandex/avisoshopSuccessUrl
//shopSuccessUrl - https://vk-pro.top/orders/success
//shopFailUrl - https://vk-pro.top/orders/fail
//shopPassword - 2dP74W44

// для платежной системы https://sprypay.ru/
define('ipnSecretKey', '9b411d5ba6400212108c6383e88f0335');
define('spShopId', '231495');

define('bitcoin_api_key', '');
define('bitcoin_api_pub', '');

class Config
{

    public static $adminId = 65217;


    //При создании нового сервиса нужно добавить сюда:
    public static $services = array(
        'admin'    => 'Admin',
        'pages'    => 'Pages',
        'system'   => 'System',
        'users'    => 'Users',
        'catalog'  => 'Catalog',
        'news'     => 'News',
        'tasks'    => 'Tasks',
        'messages' => 'Messages',
        'faq'      => 'Faq',
        'posting'  => 'Posting',
        'grabber'  => 'Grabber',
        'auto'     => 'Auto',
        'orders'   => 'Orders',
        'html'     => 'Html',
        'bot'      => 'Bot',
        'api'      => 'Api',
        'logs'     => 'Logs'
    );

    public static $links = array(
        array(
            'rel' => 'apple-touch-icon',
            'sizes' => '57x57',
            'href' => '/apple-icon-57x57.png'
        ),
        array(
            'rel' => 'apple-touch-icon',
            'sizes' => '60x60',
            'href' => '/apple-icon-60x60.png'
        ),
        array(
            'rel' => 'apple-touch-icon',
            'sizes' => '72x72',
            'href' => '/apple-icon-72x72.png'
        ),
        array(
            'rel' => 'apple-touch-icon',
            'sizes' => '76x76',
            'href' => '/apple-icon-76x76.png'
        ),
        array(
            'rel' => 'apple-touch-icon',
            'sizes' => '114x114',
            'href' => '/apple-icon-114x114.png'
        ),
        array(
            'rel' => 'apple-touch-icon',
            'sizes' => '120x120',
            'href' => '/apple-icon-120x120.png'
        ),
        array(
            'rel' => 'apple-touch-icon',
            'sizes' => '144x144',
            'href' => '/apple-icon-144x144.png'
        ),
        array(
            'rel' => 'apple-touch-icon',
            'sizes' => '152x152',
            'href' => '/apple-icon-152x152.png'
        ),
        array(
            'rel' => 'apple-touch-icon',
            'sizes' => '180x180',
            'href' => '/apple-icon-180x180.png'
        ),

        array(
            'rel' => 'icon',
            'type' => 'image/png',
            'sizes' => '16x16',
            'href' => '/favicon-16x16.png'
        )
    );
}