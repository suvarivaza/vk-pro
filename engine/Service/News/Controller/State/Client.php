<?php

namespace Service\News;

use System\HttpResponse;
use System\Service_Controller_State;

/**
 * Class Controller_State_Client
 *
 * @package Service\News
 */
abstract class Controller_State_Client extends Service_Controller_State
{
    private $_factory = null;

    public function actionPrepare()
    {
        parent::actionPrepare();
    }

    /**
     * @return void|HttpResponse
     */
    public function actionPost()
    {
        $response = new HttpResponse();
        $response->setStatus(HttpResponse::S4_NOT_FOUND);

        return $response;
    }
}
