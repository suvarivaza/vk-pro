<?php

namespace Service\Orders;

class Controller_State_Admin_Settings extends Controller_State_Admin
{
    private $_months = [
        1 => '1 месяц',
        2 => '2 месяца',
        3 => '3 месяца',
        4 => '4 месяца',
        5 => '5 месяцев',
        6 => 'полгода',
        7 => '7 месяцев',
        8 => '8 месяцев',
        9 => '9 месяцев',
        10 => '10 месяцев',
        11 => '11 месяцев',
        12 => 'год',
    ];

    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        if (isset($this->_application->menu['orders']['menu']['services'])) {
            $this->_application->menu['orders']['menu']['services']['active'] = true;
        }

        return null;
    }

    public function actionGet()
    {
        $settings = [
            'balance' => [
                'title' => 'Стоимость 10 баллов',
                'limit' => 0,
                'months' => [],
                'prices' => [],
                'groups' => [],
                'price' => 0,
                'group' => 0,
            ],
            'karma' => [
                'title' => 'Очистка кармы',
                'price' => 0,
            ],
            'auto' => [
                'title' => 'Автоведение',
                'limit' => 0,
                'months' => [],
                'prices' => [],
                'groups' => [],
                'price' => 0,
                'group' => 0,
            ],
            'grabber' => [
                'title' => 'Граббер',
                'limit' => 0,
                'months' => [],
                'prices' => [],
                'groups' => [],
                'price' => 0,
                'group' => 0,
            ],
            'posting' => [
                'title' => 'Автопостинг',
                'limit' => 0,
                'months' => [],
                'prices' => [],
                'groups' => [],
                'price' => 0,
                'group' => 0,
            ],
            'special' => [
                'title' => 'Спецзадания',
                'limit' => 0,
                'months' => [],
                'prices' => [],
                'groups' => [],
                'price' => 0,
                'group' => 0,
            ],
            'bot' => [
                'title' => 'Бот',
                'limit' => 0,
                'months' => [],
                'prices' => [],
                'groups' => [],
                'price' => 0,
                'group' => 0,
            ],
        ];

        $sets = Model_Config::getSettings();

        if (!is_array($sets)) {
            $sets = [];
        }

        $settings = array_merge($settings, $sets);

        $vars = [
            'months' => $this->_months,
            'settings' => $settings,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/settings_new', $vars));
    }

    public function actionPost()
    {
        $post = $this->_request->post->asArray();

        //лучше в БД хранить это!
        $sets = json_decode(file_get_contents(Model_Config::$settings), true); //настройки хнанятся в json формате в конфиге
        if (!is_array($sets)) $sets = [];
        $sets = array_merge($sets, $post);

        Model_Config::setSettings($sets);

        return null;
    }
}
