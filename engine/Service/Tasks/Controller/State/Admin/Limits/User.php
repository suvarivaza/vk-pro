<?php

namespace Service\Tasks;

class Controller_State_Admin_Limits_User extends Controller_State_Admin
{
    private $_limits = null;

    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        $this->_limits = json_decode(file_get_contents(Model_Config::$limitsPath), true);
    }

    public function actionGet()
    {
        $vars = [
            'limits' => $this->_limits,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/limits/user', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'limits':
                return $this->_saveLimits();
        }

        return null;
    }

    private function _saveLimits()
    {
        $limits = $this->_request->post->asArray([]);

        foreach ($limits['user'] as $type => $params) {
            foreach ($params as $name => $val) {
                $this->_limits['user'][$type][$name] = $val;
            }
        }

        file_put_contents(Model_Config::$limitsPath, json_encode($this->_limits, JSON_UNESCAPED_UNICODE));

        return null;
    }
}
