<?php
//основной скрипт для cron

include_once "config/config.php"; //основной конфиг
include_once "config/autoload.php"; //Автолоудинг движка
require __DIR__ . '/vendor/autoload.php'; //Автолоудинг composer
require __DIR__ . '/functions/functions.php'; //некототые служебные функции



\STPL::PathRegister( ENGINE_PATH . 'include/Service/System/Template/');

// ������ ���������
if ( is_array( $argv ) && count( $argv ) > 0 )
{
	foreach ( $argv as $k => $v )
	{
		if ( $k > 0 && preg_match( "@^([^\=]+)(\=[\"\']?(.*)[\"\']?)?$@", $v, $rg ) )
		{
			$params[$rg[1]] = $rg[3];
		}
	}
}


if(isset($_GET['action'])) $params['action'] = $_GET['action']; //добавил это для тестирования методов из браузера

list( $ns, $action ) = explode( ':', $params['action'] );

list( $service, $class_name ) = explode( '/', $ns, 2 );

$class_name = '\\Service\\' . $service . '\\Controller_Shell_' . ( empty( $class_name ) ? '' : str_replace( '/', '_', $class_name ) );

$obj = new $class_name();

$params['__shell_action'] = $action;
echo $obj->Action( $params );