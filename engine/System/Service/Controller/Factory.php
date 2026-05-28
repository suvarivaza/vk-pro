<?php

namespace System;

/**
 * Фабрика контроллеров сервисов
 *
 * @package System
 */
class Service_Controller_Factory
{
    /**
     * Получает экземпляр контроллера сервиса
     *
     * @static
     *
     * @param string $service Имя сервиса
     * @param string $controller_name Имя контроллера
     * @param \System\App $app
     *
     * @throws \System\Service_Controller_Exception_MissingController
     *
     * @return \System\Service_Controller_Interface
     */
    public static function getInstance($service, $controller_name, $app)
    {
        $cn = self::_getClass($service, $controller_name);

        if (false === class_exists($cn)) {
            error_log($cn);
            throw new Service_Controller_Exception_MissingController("Missing controller '" . $cn . "'");
        }

        return new $cn($app);
    }

    /**
     * Получает имя класса конроллера сервиса
     *
     * @static
     *
     * @param string $service Имя сервиса
     * @param string $controller_name Имя контроллера
     *
     * @return string
     */
    private static function _getClass($service, $controller_name)
    {
        return '\\Service\\' . $service . '\\Controller_' . str_replace('/', '_', $controller_name);
    }
}
