<?php

namespace Service\System;

class Controller_Admin_Settings extends Controller_Admin
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if (null !== $response) {
            return $response;
        }

        if (isset($this->_application->menu['settings'])) {
            $this->_application->menu['settings']['active'] = true;
        }

        return null;
    }

    /**
     * @return \System\HttpResponse|null
     */
    public function actionGet()
    {
        $it = $this->factory->settings->getAll();

        $list = [
            'address' => '',
            'email' => '',
            'email1' => '',
            'phone1Code' => '',
            'phone1Phone' => '',
            'phone1Name' => '',
            'phone2Code' => '',
            'phone2Phone' => '',
            'phone2Name' => '',
            'phone3Code' => '',
            'phone3Phone' => '',
            'phone3Name' => '',
        ];

        foreach ($it as $item) {
            $list[$item->name] = $item->value;
        }

        $this->_application->settings = $list;
        $vars = [
            'settings' => $list,
        ];

        return $this->_response->setBody(\STPL::Fetch('/admin/settings', $vars));
    }

    /**
     * @return \System\HttpResponse|null
     */
    public function actionPost()
    {
        $post = $this->_request->post->asArray();

        foreach ($post as $name => $value) {
            $setting = $this->factory->settings->getByName($name, true);

            if ($setting === null) {
                $setting = $this->factory->settings->getNewItem();
            }
            $setting->name = $name;
            $setting->value = $value;
            $this->factory->settings->save($setting);
        }

        return null;
    }
}
