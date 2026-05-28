<?php

namespace Service\Users;

class Controller_State_Admin_Karma extends Controller_State_Admin
{
    private $_types = [
        'likes' => 'Поставить лайк',
        'reposts' => 'Сделать репост',
        'comments' => 'Оставить комментарий',
        'join' => 'Вступить в сообщество, подписаться на страницу',
        'friends' => 'Заявки в друзья',
        'polls' => 'Участие в опросе ',
        'views' => 'Просмотры (охват)',
        'video' => 'Просмотры видео',
    ];

    private $_limits = null;

    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        $this->_limits = json_decode(file_get_contents(Model_Config::$karmaPath), true);

        if (isset($this->_application->menu['users']['menu']['karma'])) {
            $this->_application->menu['users']['menu']['karma']['active'] = true;
        }

        return null;
    }

    public function actionGet()
    {
        $vars = [
            'types' => $this->_types,
            'karma' => $this->_limits,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/karma', $vars));
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
        $limits = $this->_request->post->asArray([]);

        foreach ($limits['karma'] as $type => $val) {
            $this->_limits['karma'][$type] = $val;
        }

        file_put_contents(Model_Config::$karmaPath, json_encode($this->_limits, JSON_UNESCAPED_UNICODE));

        return null;
    }
}
