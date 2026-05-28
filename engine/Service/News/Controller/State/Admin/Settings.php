<?php

namespace Service\News;

use STPL;

class Controller_State_Admin_Settings extends Controller_State_Admin
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        if (isset($this->_application->menu['news']['menu']['settings'])) {
            $this->_application->menu['news']['menu']['settings']['active'] = true;
        }

        return null;
    }

    public function actionGet()
    {
        $settings = [
            'show' => false,
        ];

        $sets = Model_Config::GetSettings();

        $settings = array_merge($settings, $sets);

        $vars = [
            'settings' => $settings,
        ];

        return $this->_response->setBody(STPL::Fetch('admin/settings', $vars));
    }

    public function actionPost()
    {
        $settings = [
            'show' => false,
        ];
        $sets = $this->_request->post['settings']->asArray([]);
        $settings = array_merge($settings, $sets);
        Model_Config::SetSettings($settings);

        return $this->_response->setLocation($this->_request->server['REDIRECT_URL']->string(''));
    }
}
