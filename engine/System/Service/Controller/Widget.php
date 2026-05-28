<?php

namespace System;

/**
 * Базовый класс контроллера виджета сервиса.
 *
 * @todo make this class to subclass Service_Controller_State.
 *
 * @package System
 */
abstract class Service_Controller_Widget implements \System\Service_Controller_Interface
{
    /** @var int */
    protected $_x_meta_cache_lifetime = 5;

    /**
     * Имя сервиса
     *
     * @var string
     */
    protected $_service = '';

    /**
     * Параметры
     *
     * @var array
     */
    protected $_params = [];

    /** @var \System\HttpResponse */
    protected $_response;

    /** @var HttpRequest */
    protected $_request;

    /** @var \System\App */
    protected $_application;

    /**
     * Конструктор
     */
    public function __construct(\System\App $app)
    {
        $className = get_class($this);
        list(, $service) = explode('\\', $className);

        if (!empty($service)) {
            $this->_service = $service;
        }

        \STPL::PathRegister(ENGINE_PATH . 'engine/Service/' . $this->_service . '/Template/');

        $this->_application = $app;
        $this->_request = new \System\HttpRequest('', $_GET, $_POST, $_COOKIE, $_SERVER);
        $this->_response = new \System\HttpResponse();
    }

    /**
     * @param array|null $params
     *
     * @return HttpResponse|void|null
     *
     * @throws \Lib_Exception_Logic
     */
    public function Action($params = null)
    {
        $this->_params = $params;

        if (!($response = $this->actionPrepare())) {
            $response = $this->actionGet();
        }

        if (null !== $response) {
            if (!$response instanceof \System\HttpResponse) {
                throw new \Lib_Exception_Logic('Internal Widget Error: response must be instance of \System\HttpResponse in class: ' . get_class($this));
            }

            if ($this->_application->Title) {
                $response->setMetaHeaders($this->_application->Title);

                if (!$response->getHeader(\System\HttpResponse::X_META_CACHE_KEY)) {
                    $response->setHeader(\System\HttpResponse::X_META_CACHE_KEY, $this->_getXCacheKey());
                }

                if (!$response->getHeader(\System\HttpResponse::X_META_CACHE_LIFETIME)) {
                    $response->setHeader(\System\HttpResponse::X_META_CACHE_LIFETIME, $this->_getXMetaCacheLifetime());
                }
            }
        }

        return $response;
    }

    /**
     * @return string
     */
    protected function _getXCacheKey()
    {
        return $this->_service . '|' . get_class($this) . '|' . md5(serialize($this->_params));
    }

    /**
     * @return int
     */
    protected function _getXMetaCacheLifetime()
    {
        return $this->_x_meta_cache_lifetime;
    }

    /**
     * Обработчик инициализации (вызывается до всех действий).
     *
     * @return \System\HttpResponse|null
     */
    public function actionPrepare()
    {
    }

    /**
     * Обработчик запросов.
     *
     * @return \System\HttpResponse|null
     */
    abstract public function actionGet();
}
