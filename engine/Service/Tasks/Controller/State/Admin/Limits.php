<?php

namespace Service\Tasks;

class Controller_State_Admin_Limits extends Controller_State_Admin
{
    private $_limits = null;

    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        if (isset($this->_application->menu['tasks']['menu']['limits'])) {
            $this->_application->menu['tasks']['menu']['limits']['active'] = true;
        }

        $this->_limits = json_decode(file_get_contents(Model_Config::$limitsPath), true);

        return null;
    }

    public function actionGet()
    {
        $vars = [
            'limits' => $this->_limits,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/limits', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'limits':
                return $this->_saveLimits();
        }
    }

    private function _saveLimits()
    {
        $limits = $this->_request->post->asArray([]);

        foreach ($limits['groups']['counts'] as $i => $val) {
            $this->_limits['groups']['counts'][$i] = $val;
        }

        foreach ($limits['groups']['limits'] as $i => $val) {
            $this->_limits['groups']['limits'][$i] = $val;
        }

        file_put_contents(Model_Config::$limitsPath, json_encode($this->_limits, JSON_UNESCAPED_UNICODE));

        return null;
    }
}
