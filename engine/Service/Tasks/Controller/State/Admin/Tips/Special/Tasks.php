<?php

namespace Service\Tasks;

/**
 * Class Controller_State_Admin_Blacklist
 *
 * @package Service\Tasks
 */
class Controller_State_Admin_Tips_Special_Tasks extends Controller_State_Admin
{
    private $_tip_path = ENGINE_PATH . 'engine/Service/Tasks/Template/controls/special/';

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

        $texts = [];

        foreach ($this->_types as $type => $title) {
            if (is_file($this->_tip_path . $type . '.php')) {
                $texts[$type] = strval(file_get_contents($this->_tip_path . $type . '.php'));
            } else {
                $texts[$type] = '';
            }

            foreach ($this->_vkTypes as $vkType => $vkTitle) {
                if (is_file($this->_tip_path . $type . '_' . $vkType . '.php')) {
                    $texts[$type . '_' . $vkType] = strval(file_get_contents($this->_tip_path . $type . '_' . $vkType . '.php'));
                } else {
                    $texts[$type . '_' . $vkType] = '';
                }
            }
        }

        $vars = [
            'type' => $this->_params['type'],
            'vkTypes' => $this->_vkTypes,
            'text' => $texts[$this->_params['type']],
            'texts' => $texts,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/tips/special/tasks', $vars));
    }

    public function actionPost()
    {
        if ($this->_request->post['vkType']->string('')) {
            $name = $this->_params['type'] . '_' . $this->_request->post['vkType']->string('');
            file_put_contents($this->_tip_path . $name . '.php', $this->_request->post[$name]->string());
        } else {
            $name = $this->_params['type'];
            file_put_contents($this->_tip_path . $name . '.php', $this->_request->post[$name]->string());
        }

        return $this->_response->setLocation('/admin/tasks/tips/special/' . $this->_params['type'] . '#' . $this->_request->post['vkType']->string(''));
    }
}
