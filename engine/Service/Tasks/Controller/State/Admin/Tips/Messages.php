<?php

namespace Service\Tasks;

/**
 * Class Controller_State_Admin_Blacklist
 *
 * @package Service\Tasks
 */
class Controller_State_Admin_Tips_Messages extends Controller_State_Admin
{
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

        if (isset($this->_application->menu['messages'])) {
            $this->_application->menu['messages']['active'] = true;
        }

        $types = [
            'likes' => 'Поставил лайк',
            'reposts' => 'Сделал репост',
            'join' => 'Подписался на группу',
            'friends' => 'Заявка в друзья',
            'comments' => 'Оставил комментарий',
            'views' => 'Просмотрел запись',
            'polls' => 'Участвовал в голосовании',
            'video' => 'Просмотрел видео',
        ];

        $settings = [];

        foreach ($types as $type => $title) {
            $settings['message_' . $type] = '';
        }

        $set = $this->_application->settings;

        foreach ($set as $name => $val) {
            $settings[$name] = $val;
        }

        $vars = [
            'types' => $types,
            'settings' => $settings,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/messages', $vars));
    }

    public function actionPost()
    {
        $types = [
            'likes' => 'Поставил лайк',
            'reposts' => 'Сделал репост',
            'join' => 'Подписался на группу',
            'friends' => 'Заявка в друзья',
            'comments' => 'Оставил комментарий',
            'views' => 'Просмотрел запись',
            'polls' => 'Участвовал в голосовании',
            'video' => 'Просмотрел видео',
        ];

        foreach ($types as $type => $title) {
            $setting = $this->factorySystem->settings->getByName('message_' . $type, true);

            if ($setting === null) {
                $setting = $this->factorySystem->settings->getNewItem();
            }
            $setting->name = 'message_' . $type;
            $setting->value = $this->_request->post[$setting->name]->string();
            $this->factorySystem->settings->save($setting);
        }

        $this->_application->getSettings();

        return $this->_response->setLocation('/admin/tasks/messages');
    }
}
