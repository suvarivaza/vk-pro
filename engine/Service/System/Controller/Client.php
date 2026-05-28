<?php

namespace Service\System;

/**
 * Class Controller_Client
 *
 * @package Service\System
 */
abstract class Controller_Client extends \System\Service_Controller_State
{
    /**
     * Обработчик POST-запросов
     *
     * @return void|\System\HttpResponse
     */
    public function actionPost()
    {
        return $this->_response->setStatus(\System\HttpResponse::S4_METHOD_NOT_ALLOWED);
    }
}
