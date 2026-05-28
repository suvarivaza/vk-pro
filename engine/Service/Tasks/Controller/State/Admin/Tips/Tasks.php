<?php

namespace Service\Tasks;

/**
 * Class Controller_State_Admin_Blacklist
 *
 * @package Service\Tasks
 */
class Controller_State_Admin_Tips_Tasks extends Controller_State_Admin
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
        $this->_application->Title->addScripts(
            [
                '/js/jquery/tinymce/tinymce.min.js',
            ]
        );

        $this->_application->Title->addStyles([
            '/js/jquery/tinymce/plugins/moxiemanager/skins/lightgray/skin.min.css',
        ]);

        if (isset($this->_application->menu['tips'])) {
            $this->_application->menu['tips']['active'] = true;
        }

        $settings = [];

        foreach ($this->_types as $type => $title) {
            $settings['tip_' . $type] = '';

            foreach ($this->_vkTypes as $vkType => $vkTitle) {
                $settings['tip_' . $type . '_' . $vkType] = '';
            }
        }

        $set = $this->_application->settings;

        foreach ($set as $name => $val) {
            $settings[$name] = $val;
        }
        $texts = [];

        foreach ($this->_vkTypes as $vkType => $vkTitle) {
            $texts['tip_' . $this->_params['type'] . '_' . $vkType] = $this->_application->settings['tip_' . $this->_params['type'] . '_' . $vkType];
        }

        $vars = [
            'type' => $this->_params['type'],
            'vkTypes' => $this->_vkTypes,
            'texts' => $texts,
            'text' => $this->_application->settings['tip_' . $this->_params['type']],
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/tips/tasks', $vars));
    }

    public function actionPost()
    {
        $setting = $this->factorySystem->settings->getByName('tip_' . $this->_params['type'], true);

        if ($setting === null) {
            $setting = $this->factorySystem->settings->getNewItem();
        }
        $setting->name = 'tip_' . $this->_params['type'];
        $setting->value = $this->_request->post[$setting->name]->string();
        $this->factorySystem->settings->save($setting);

        if ($this->_request->post['vkType']->string('')) {
            $name = 'tip_' . $this->_params['type'] . '_' . $this->_request->post['vkType']->string('');
            $setting = $this->factorySystem->settings->getByName($name, true);

            if ($setting === null) {
                $setting = $this->factorySystem->settings->getNewItem();
            }
            $setting->name = $name;
            $setting->value = $this->_request->post[$setting->name]->string();
            $this->factorySystem->settings->save($setting);
        }

        return $this->_response->setLocation('/admin/tasks/tips/' . $this->_params['type'] . '#' . $this->_request->post['vkType']->string(''));
    }
}
