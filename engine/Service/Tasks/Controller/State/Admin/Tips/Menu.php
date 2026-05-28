<?php

namespace Service\Tasks;

/**
 * Class Controller_State_Admin_Tips_Menu
 *
 * @package Service\Tasks
 */
class Controller_State_Admin_Tips_Menu extends Controller_State_Admin
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        if (isset($this->_application->menu['tasks'])) {
            $this->_application->menu['tasks']['active'] = false;
        }

        if (isset($this->_application->menu['tips'])) {
            $this->_application->menu['tips']['active'] = true;
        }

        if (isset($this->_application->menu['tips']['menu']['tips'])) {
            $this->_application->menu['tips']['menu']['tips']['active'] = true;
        }

        return null;
    }

    public function actionGet()
    {
        $settings = [
            'auto_time' => 1000,
            'auto_text' => '',
            'posting_time' => 1000,
            'postiong_text' => '',
            'grabber_time' => 1000,
            'grabber_text' => '',
            'special_time' => 1000,
            'special_text' => '',
            'bot_time' => 1000,
            'bot_text' => '',
        ];

        foreach ($settings as $name => $val) {
            if (isset($this->_application->settings[$name])) {
                $settings[$name] = $this->_application->settings[$name];
            }
        }

        $vars = [
            'settings' => $settings,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/tips/menu', $vars));
    }

    public function actionPost()
    {
        $settings = [
            'auto_time' => 1000,
            'auto_text' => '',
            'posting_time' => 1000,
            'postiong_text' => '',
            'grabber_time' => 1000,
            'grabber_text' => '',
            'special_time' => 1000,
            'special_text' => '',
            'bot_time' => 1000,
            'bot_text' => '',
        ];

        $data = $this->_request->post->asArray();

        foreach ($settings as $name => $val) {
            if (isset($data[$name])) {
                $val = $data[$name];
            }

            $setting = $this->factorySystem->settings->getByName($name, true);

            if ($setting === null) {
                $setting = $this->factorySystem->settings->getNewItem();
                $setting->name = $name;
            }
            $setting->value = $val;

            $this->factorySystem->settings->save($setting);
        }

        return $this->_response->setLocation('/admin/tasks/tips/menu');
    }
}
