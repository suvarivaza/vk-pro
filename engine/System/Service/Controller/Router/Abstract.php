<?php

namespace System;

abstract class Service_Controller_Router_Abstract implements Service_Controller_Router_Interface
{
    protected $_params = [];

    protected $_state = '';

    protected $_application = null;

    /**
     * Амдинистративный маршрут
     *
     * @var bool
     */
    protected $_admin = false;

    protected $_fromAdmin = false;

    protected $_service = false;
    protected $_fromService = '';

    final public function __construct(\System\App $app)
    {
        $this->_application = $app;
    }

    // Главнй метод контролена роутинга, запускается в index.php
    // не позволяем переоопределять в дочерних классах с помощью final
    final public function Action($params = [])
    {


        //Выбрасываем ошибку если нет __routing_path
        if (false === isset($params['__routing_path']) || false === is_string($params['__routing_path'])) {
            throw new \Lib_Exception_InvalidArgument('Path must be in params');
        }


        //Проверяем прописал ли такой роут, если нет вернем ответ сервера 404
        if (false === ($state = $this->Route($params['__routing_path']))) {
            $response = new \System\HttpResponse();
            return $response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }

        //Закрываем досуп в админские роуты если у пользователя нет прав админа
        if ($this->_admin === true && !$this->_fromAdmin) {
            $response = new \System\HttpResponse();
            return $response->setStatus(\System\HttpResponse::S4_FORBIDDEN);
        }

        //защита от прямого включения php файлов сервиса
        if ($this->_service !== false && $this->_service != $this->_fromService) {
            $response = new \System\HttpResponse();
            return $response->setStatus(\System\HttpResponse::S4_FORBIDDEN);
        }


        list(, $service) = explode('\\', get_class($this));

        //получаем экземпляр контроллера запрошенного сервиса
        $controller = Service_Controller_Factory::getInstance($service, $state, $this->_application);

        // Запускаем контроллер вызывая у него метод Action
        try {

            $result = $controller->Action($this->_params);

            $this->_state = self::getStateFromController(
                $controller
            );
        } catch (\Exception $e) {
            $this->_state = self::getStateFromController(
                $controller
            );

            throw $e;
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getExecutedState()
    {
        return $this->_state ?: get_class($this);
    }

    /**
     * @param Service_Controller_Interface $controller
     *
     * @return string
     */
    public static function getStateFromController(Service_Controller_Interface $controller)
    {
        if ($controller instanceof Service_Controller_Router_Interface) {
            return $controller->getExecutedState();
        } else {
            return get_class($controller);
        }
    }
}
