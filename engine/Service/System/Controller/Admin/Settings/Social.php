<?php

namespace Service\System;

class Controller_Admin_Settings_Sitemap extends Controller_Admin
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        return null;
    }

    public function actionGet()
    {
        $this->_application->getSettings();

        $social = $this->_application->settings['social'];
        $social = json_decode($social, true);

        $vars = [
            'social' => $social,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/settings/social', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'save':
                return $this->_save();
        }

        return $this->_response->setStatus(\System\HttpResponse::S4_METHOD_NOT_ALLOWED);
    }

    private function _save()
    {
        $arr = $this->_request->post['social']->asArray();
        $json = json_encode($arr);

        $setting = $this->factory->settings->getByName('social', true);

        if ($setting === null) {
            $setting = $this->factory->settings->getNewItem();
        }
        $setting->name = 'social';
        $setting->value = $json;
        $this->factory->settings->save($setting);

        return null;
    }
}
