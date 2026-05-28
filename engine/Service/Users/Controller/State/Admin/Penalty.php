<?php

namespace Service\Users;

class Controller_State_Admin_Penalty extends Controller_State_Admin
{
    private $_types = [
        'likes' => 'Убрал лайк',
        'reposts' => 'Убрал репост',
        'comments' => 'Удалил комментарий',
        'join' => 'Отписался от группы',
        'friends' => 'Убрал заявку в друзья',
    ];

    private $_limits = null;

    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        $this->_limits = json_decode(file_get_contents(Model_Config::$karmaPath), true);

        if (isset($this->_application->menu['users']['menu']['penalty'])) {
            $this->_application->menu['users']['menu']['penalty']['active'] = true;
        }

        return null;
    }

    public function actionGet()
    {
        $vars = [
            'types' => $this->_types,
            'penatly' => $this->_limits,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/penalty', $vars));
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

        foreach ($limits['penatly'] as $type => $val) {
            $this->_limits['penatly'][$type] = $val;
        }

        if ($limits['penalty_day']) {
            $this->_limits['penalty_day'] = $limits['penalty_day'];
        }

        file_put_contents(Model_Config::$karmaPath, json_encode($this->_limits, JSON_UNESCAPED_UNICODE));

        return null;
    }
}
