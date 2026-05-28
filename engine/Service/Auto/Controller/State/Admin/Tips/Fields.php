<?php

namespace Service\Auto;

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

        if (isset($this->_application->menu['tips']['menu']['auto'])) {
            $this->_application->menu['tips']['menu']['auto']['active'] = true;
        }

        return null;
    }

    public function actionGet()
    {
        $this->_application->menu['auto']['active'] = true;
        $tips = [
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

            'fromGroupOnly-time' => 1000,
            'fromGroupOnly-text' => '',

            'attachmentType-time' => 1000,
            'attachmentType-text' => '',

            'adsOut-time' => 1000,
            'adsOut-text' => '',

            'specialId-time' => 1000,
            'specialId-text' => '',

            'counts-time' => 1000,
            'counts-text' => '',

            'balanceLimit-time' => 1000,
            'balanceLimit-text' => '',

            'title-time' => 1000,
            'title-text' => '',
        ];

        $sets = json_decode(file_get_contents(Model_Config::$settings), true);

        $tips = array_merge($tips, $sets['tips']);

        $vars = [
            'tips' => $tips,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/tips/fields', $vars));
    }

    public function actionPost()
    {
        $data = $this->_request->post->asArray();
        $sets = json_decode(file_get_contents(Model_Config::$settings), true);
        $sets['tips'] = $data;

        file_put_contents(Model_Config::$settings, json_encode($sets, JSON_UNESCAPED_UNICODE));

        return $this->_response->setLocation('/admin/auto/tips/fields');
    }
}
