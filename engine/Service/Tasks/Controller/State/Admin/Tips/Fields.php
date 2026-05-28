<?php

namespace Service\Tasks;

/**
 * Class Controller_State_Admin_Tips_Menu
 *
 * @package Service\Tasks
 */
class Controller_State_Admin_Tips_Fields extends Controller_State_Admin
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
            'url-time' => 1000,
            'url-text' => '',

            'minKarma-time' => 1000,
            'minKarma-text' => '',

            'sex-time' => 1000,
            'sex-text' => '',

            'age-time' => 1000,
            'age-text' => '',

            'city-time' => 1000,
            'city-text' => '',

            'relation-time' => 1000,
            'relation-text' => '',

            'avatarCount-time' => 1000,
            'avatarCount-text' => '',

            'filled-time' => 1000,
            'filled-text' => '',

            'pageAge-time' => 1000,
            'pageAge-text' => '',

            'followersCount-time' => 1000,
            'followersCount-text' => '',

            'interestingPage-time' => 1000,
            'interestingPage-text' => '',

            'frequencyPost-time' => 1000,
            'frequencyPost-text' => '',

            'prior-time' => 1000,
            'prior-text' => '',
        ];

        foreach ($settings as $name => $val) {
            if (isset($this->_application->settings[$name])) {
                $settings[$name] = $this->_application->settings[$name];
            }
        }

        $vars = [
            'settings' => $settings,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/tips/fields', $vars));
    }

    public function actionPost()
    {
        $settings = [
            'url-time' => 1000,
            'url-text' => '',

            'minKarma-time' => 1000,
            'minKarma-text' => '',

            'sex-time' => 1000,
            'sex-text' => '',

            'age-time' => 1000,
            'age-text' => '',

            'city-time' => 1000,
            'city-text' => '',

            'relation-time' => 1000,
            'relation-text' => '',

            'avatarCount-time' => 1000,
            'avatarCount-text' => '',

            'filled-time' => 1000,
            'filled-text' => '',

            'pageAge-time' => 1000,
            'pageAge-text' => '',

            'followersCount-time' => 1000,
            'followersCount-text' => '',

            'interestingPage-time' => 1000,
            'interestingPage-text' => '',

            'frequencyPost-time' => 1000,
            'frequencyPost-text' => '',

            'prior-time' => 1000,
            'prior-text' => '',
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

        return $this->_response->setLocation('/admin/tasks/tips/fields');
    }
}
