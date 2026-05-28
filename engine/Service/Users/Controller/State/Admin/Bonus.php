<?php

namespace Service\Users;

class Controller_State_Admin_Bonus extends Controller_State_Admin
{
    private $_bonus = null;

    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        $this->_bonus = Model_Config::GetBonusSettings();

        if (isset($this->_application->menu['users']['menu']['bonus'])) {
            $this->_application->menu['users']['menu']['bonus']['active'] = true;
        }

        return null;
    }

    public function actionGet()
    {
        $vars = [
            'bonus' => $this->_bonus,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/bonus', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();


        switch ($action) {
            case 'save':
                return $this->_save();
        }

        return null;
    }

    private function _save()
    {
        $bonus = $this->_request->post['bonus']->asArray([]);


        foreach ($bonus as $type => $val) {
            $this->_bonus[$type] = $val;
        }

        Model_Config::SetBonusSettings($this->_bonus);

        return null;
    }
}
