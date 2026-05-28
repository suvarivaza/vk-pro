<?php
namespace Service\Api;

class Controller_State_Client_Sync extends Controller_State_Client
{
    public function actionPrepare()
    {
        return null;
    }

    public function actionGet()
    {
        $data = json_decode( file_get_contents('php://input') );
        error_log(print_r( $data, true ));

        error_log(print_r($this->_request->post->asArray(), true));
        return $this->_response->setJson($this->_request->post->asArray());
    }

    public function actionPost()
    {
        return null;
    }

}