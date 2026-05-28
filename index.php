<?php

//echo "OK"; die;
ini_set('memory_limit', '512M');


if(!session_status()) {
    session_start();
}


include "config/config.php"; //основной конфиг
include "config/autoload.php"; //Автолоудинг движка
require __DIR__ . '/vendor/autoload.php'; //Автолоудинг composer
require __DIR__ . '/functions/functions.php'; //некоготые служебные функции


//callback.vk-pro.top используется как колбек сервер для апи ВК (вк на него шлет колбек ответы)
//На callback.vk-pro.top разрешаем только запросы вида https://callback.vk-pro.top/auto/callback
if($_SERVER['HTTP_HOST'] === 'callback.vk-pro.top' and $_SERVER['REQUEST_URI'] !== '/auto/callback') die('Access is denied');

//Что то лишнее??
//if ($_SERVER['HTTP_HOST'] == 'www.' . DOMAIN)
//{
//    $response = new \System\HttpResponse();
//    $response->setLocation('https://' . DOMAIN);
//    echo $response;
//    exit;
//}

//Создаем приложение
$app = new \System\App();

//Задаем тайтлы
$app->Title->append('Бесплатная накрутка лайков, друзей и подписчиков в VK без заданий');
$app->Title->Description = 'Качественная, бесплатная и безопасная накрутка вконтакте - друзей, подписчков, лайков, репостов, раскрутка и монетизация групп vk - автопопстинг, граббер - автоведение групп. Быстрый вывод видео в топ, накрутка просмотров вк!';
$app->Title->Keywords = 'накрутка, лайков, репостов, накрутка репостов, друзей, подписчиков, накрутка подписчиков, автопостинг вк, граббер vk, автоведение групп вконтакте, vk, vkontakte, раскрутка, монетизация групп, вывод видео в топ, вывод в топ, качественная накрутка, накрутка вконтакте, безопасная накрутка, репосты, друзья, лайки, просмотры, подписчики';
$app->Title->add('meta', array(
    'charset' => 'UTF-8'
));

//Подключаем различные иконки
foreach (Config::$links as $link) {
    $app->Title->add('link', $link);
}

//Задаем мета теги
$app->Title->add('meta', array(
    'name' => 'viewport',
    'content' => 'width=device-width, initial-scale=1',
));

//$app->Title->add('meta', array(
//    'name' => 'yandex-verification',
//    'content' => 'a1a382774268ce36',
//));

$app->Title->add('meta', [
    'name' => 'msvalidate.01',
    'content' => 'C75F14735539F953D04F1DFE5EB286A0'
]);
$app->Title->add('meta', [
    'name' => 'wmail-verification',
    'content' => 'a246fed7bf99c88ff04ccfce0b144d4e'
]);
$app->Title->add('meta', array(
    'name' => 'google-site-verification',
    'content' => 'NaF6QcXFl3HWvcK1sYWGnB9sY_ta0sZLTMAz6PLCHcY'
));

$app->Title->add('meta', array(
    'name' => 'robots',
    'content' => 'index, follow'
));

$app->Title->add('link', array(
    'rel'  => 'icon',
    'href' => '/favicon.png?1',
    'type' => 'image/png'
));

$app->Title->add('link', array(
    'rel'  => 'shortcut icon',
    'href' => '/favicon.png?1',
    'type' => 'image/png'
));

//Подключаем стили
$app->Title->addStyles([
    '/css/fonts.min.css',
    '/css/bootstrap.min.css',
    //'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',
    //'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css',
    '/css/lightgallery.css',
    '/css/lg-transitions.min.css',
]);


//Задаем $_SERVER['REDIRECT_URL'] если не задано
$_SERVER['REDIRECT_URL'] = isset($_SERVER['REDIRECT_URL'])?$_SERVER['REDIRECT_URL']:$_SERVER['DOCUMENT_URI'];

//Условие для плагина moxiemanager
if ($_SERVER['REDIRECT_URL'] == '/js/jquery/tinymce/plugins/moxiemanager/api.php')
{
    $_SERVER['REDIRECT_URL'] = '/system/moxiemanager/api';
}

//Распарсиваем название запрашиваемого сервиса из URL
$path = trim($_SERVER['REDIRECT_URL']);
list(,$service) = explode('/', $path);


//для тестирования скриптов запускаемых кроном из shell.php
if($_SERVER['REDIRECT_URL'] === '/shell-test') {
    include 'shell.php';
}


if ( $app->UserID === Config::$adminId) $GLOBALS['isSuperUser'] = true;
else $GLOBALS['isSuperUser'] = false;

//подключаем и регистрируем обработчик ошибок
require_once VAR_PATH . '/engine/ErrorHandler.php';
use \Suvarivaza\ErrorHandler;
$ErrorHandler = new ErrorHandler;
$ErrorHandler->register();


//Формируем $routing_path
//Для сервисов:
if ($service)
{
    $routing_path = substr($path, strlen($service) + 2);

    if ($routing_path === false)
        $routing_path = '';

    //Если сервис описан в конфигах то прокускаем
    if (isset(Config::$services[$service]))
    {
        $service = Config::$services[$service];
    }
    else
    {
        $service = 'Pages';
        $routing_path = $path;
    }


    $class = '\\Service\\' . $service . '\\Controller_Router'; //Controller_Router вызываемого сервиса

}
//Если не сервис = главная страница
else
{
    $routing_path = '';
    $class = '\\Service\\System\\Controller_Router'; //Контроллер главной страницы
}


//Выводим эти скрипты во всех сервисах кроме админки
if ($service != 'Admin')
{
    $app->Title->addScripts([
        '/js/jquery/jquery-1.11.0.min.js',
        '/js/jquery/jquery.cookie.js',
        '/js/jquery/jquery.maskedinput.min.js',
        '/js/bootstrap.min.js',
        '//cdn.jsdelivr.net/npm/sweetalert2@10',
        '/js/sweetalert2.js',
        '/js/lg/lightgallery.min.js',
        '/js/lg/lg-thumbnail.min.js',
        '/js/lg/lg-fullscreen.min.js',
        '/js/floating-labels.min.js',
        '/js/init.min.js',
        '/js/messages.min.js?ver=1.1',
        '/js/login.js?' . filemtime($_SERVER['DOCUMENT_ROOT'] . '/js/login.js')
    ]);

    $app->Title->addStyle('/css/styles.min.css?v=2.0');
    $app->Title->addStyle('/css/custom.css');
}


//Если польлзователь не авторизирован добавляем скрипты для логина
//ulogin.ru больше не работает! не загружаем!
//if (!$app->UserIsAuth())
//{
//    $app->Title->addScripts([
//        '//ulogin.ru/js/ulogin.js',
//    ]);
//}


//https://ulogin.ru/auth.php?name=vkontakte&window=1&lang=ru&fields=first_name,last_name,email,sex,photo,photo_big&force_fields=&popup_css=&host=vk-pro.top&optional=city,country,phone,nickname,bdate&redirect_uri=https%3A%2F%2Fvk-pro.top%2Fusers%2Fsocial%2Fauth&verify=&callback=&screen=1536x864&url=&providers=vkontakte&hidden=&m=0&page=https%3A%2F%2Fvk-pro.top%2F&icons_32=&icons_16=&theme=classic&client=&version=4


//Создаем экземпляр класса роутера и вызываем у него метод Action
/** @var \System\Service_Controller_Router_Rewrite $router */
$router = new $class($app);
$response = $router->Action(array('__routing_path' => $routing_path));


//Если статус ответа отличный от 200 формируем станицу 404
//Используем шаблонизатор STPL
if ($response->getStatus() != \System\HttpResponse::S2_OK)
{

    \STPL::RestorePaths(array());
    \STPL::PathRegister( ENGINE_PATH . 'engine/Service/System/Template/');
    $response->setBody(\STPL::Fetch(
        'blank', array(
            'service' => $service,
            'path' => $routing_path,
            'app' => $app,
            'html' => \STPL::Fetch('controls/404', array())
        )
    ));

    echo $response;
    exit;
}



//Клонируем объект ответа
$res = clone $response;

//Проверяем тип контента
if (!in_array($res->getContentType(), array('application/json', 'text/javascript', 'text/xml', 'application/xml', 'image/png', 'text/csv', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')) )
{
    $template = 'blank';

    //Для главной страницы задаем шаблон main_new.php
    if (!$service)
    {
        $app->Title->addStyle('/css/main.min.css?1.3');
        $template = 'main_new';
    }
    //Для страниц шаблон pages.php
    if ($service == 'Pages')
    {
        $app->Title->addStyle('/css/pages.min.css?1.3');
        $template = 'pages';
    }

    //Вызываем заданный шаблон с помощью шаблонизатора pages
    \STPL::RestorePaths(array());
    \STPL::PathRegister( ENGINE_PATH . 'engine/Service/System/Template/'); //Регистрируем путь до файла с шаблоном
    $res->setBody(
        \STPL::Fetch(
            $template, array(
                'service' => $service, //Имя сервиса
                'path' => $routing_path, //путь до сервиса
                'app' => $app,
                'html' => $response->getBody()
            )
        )
    );
}


echo $res;
