<?php

namespace Service\Messages;

class Controller_State_Admin_Config extends Controller_State_Admin
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        if (isset($this->_application->menu['messages']['menu']['system'])) {
            $this->_application->menu['messages']['menu']['system']['active'] = true;
        }

        return null;
    }

    public function actionGet()
    {
        $config = Model_Config::$types;

        $sets = Model_Config::getConfig();

        if (!is_array($sets)) {
            $sets = [];
        }

        foreach ($config as $type => $data) {
            foreach ($data['types'] as $name => $value) {
                if (isset($sets[$type]['types'][$name]['text'])) {
                    $config[$type]['types'][$name]['text'] = $sets[$type]['types'][$name]['text'];
                }
            }
        }

        $vars = [
            'config' => $config,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/config', $vars));
    }

    public function actionPost()
    {
        $post = $this->_request->post->asArray();

        $sets = Model_Config::GetConfig();

        if (!is_array($sets)) {
            $sets = [];
        }

        $sets = array_merge($sets, $post);

        Model_Config::SetConfig($sets);

        return null;
    }
}
