<?php

namespace System;

/**
 * Базовый класс маршрутизатора сервиса.
 * Отвечает за вызов Action() заданного контроллера, в зависимости от url.
 * Контроллером может быть также и другой маршрутизатор.
 *
 * @package System
 */
class Service_Controller_Router_Rewrite extends Service_Controller_Router_Abstract
{
    /**
     * Таблица маршрутизации
     *
     * @var array
     */
    protected $routes = [];

    /**
     * @param string $path
     *
     * @return bool|string
     */
    public function Route($path)
    {
        $state = false;
        $this->_params = [
            'admin' => false,
        ];

        // перебираем существующие роуты и проверяем на соотвествие с полученным $path
        foreach ($this->routes as $k => $v) {
            if (isset($v['string'])) {
                if ($v['string'] === $path) {
                    $state = $k;
                    $this->_admin = isset($v['admin']) && $v['admin']; //Проверяем есть ли у роута доступ в админку
                    $this->_service = $v['service'] ?? false;

                    if (true === $this->_admin) {
                        $this->_params['admin'] = true;
                    }

                    break;
                }
            } elseif (isset($v['regexp'])) {
                if (preg_match($v['regexp'], $path, $matches)) {
                    $state = $k;

                    if (isset($v['matches']) && is_array($v['matches'])) {
                        foreach ($v['matches'] as $mk => $mv) {
                            $this->_params[$mv] = null;

                            if (isset($matches[$mk])) {
                                $this->_params[$mv] = $matches[$mk];
                            }
                        }
                    }
                    $this->_admin = isset($v['admin']) && $v['admin'];
                    $this->_service = $v['service'] ?? false;

                    if (true === $this->_admin) {
                        $this->_params['admin'] = true;
                    }
                    break;
                }
            }
        }

        if (!$state) {
            return false;
        }

        return $state;
    }

    /**
     * Возвращает дополнительные параметры маршрутизации
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->_admin;
    }

    public function setAdmin()
    {
        $this->_fromAdmin = true;
    }

    public function setServices($service)
    {
        $this->_fromService = $service;
    }
}
