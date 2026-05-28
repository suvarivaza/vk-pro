<?php

namespace System;

/**
 * Интерфейс контроллера сервиса
 *
 * @package System
 */
interface Service_Controller_Interface
{
    /**
     * Действие контроллера
     *
     * @abstract
     *
     * @param array $params
     *
     * @return \System\HttpResponse
     */
    public function Action($params = []);
}
