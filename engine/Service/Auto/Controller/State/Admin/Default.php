<?php

namespace Service\Auto;

class Controller_State_Admin_Default extends Controller_State_Admin
{
    public function actionPrepare()
    {
        parent::actionPrepare();

        return null;
    }

    public function actionGet()
    {
        $config = Model_Config::getConfig();

        $vars = [
            'config' => $config,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/default', $vars));
    }

    public function actionPost()
    {
        $post = $this->_request->post->asArray();
        $config = Model_Config::getConfig();
        $config = array_merge($config, $post);
        Model_Config::saveConfig($config);

        return null;
    }
}
