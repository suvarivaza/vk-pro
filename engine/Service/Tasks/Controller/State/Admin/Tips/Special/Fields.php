<?php

namespace Service\Tasks;

/**
 * Class Controller_State_Admin_Tips_Menu
 *
 * @package Service\Tasks
 */
class Controller_State_Admin_Tips_Special_Fields extends Controller_State_Admin
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

        if (isset($this->_application->menu['tips']['menu']['special'])) {
            $this->_application->menu['tips']['menu']['special']['active'] = true;
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

        $sets = json_decode(file_get_contents(Model_Config::$specialTips), true);

        $settings = array_merge($settings, $sets);

        $vars = [
            'settings' => $settings,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/tips/special/fields', $vars));
    }

    public function actionPost()
    {
        $data = $this->_request->post->asArray();

        file_put_contents(Model_Config::$specialTips, json_encode($data, JSON_UNESCAPED_UNICODE));

        return $this->_response->setLocation('/admin/tasks/tips/special/fields');
    }
}
