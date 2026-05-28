<?php

namespace Service\Bot;

use Service\Tasks\Model_Users_User;
use Service\Users\Model_Notifications_Notification;
use Service\Users\Model_Users_Balances_Balance;
use STPL;
use System\HttpResponse;

class Controller_State_Client_Default extends Controller_State_Client
{
    /** @var Model_Bots_Bot */
    protected $_bot = null;
    protected $_success = null;

    protected $_fields = [
        'comments' => [
            'limit' => true,
            'title' => 'Оставлять комментарии',
        ],
        'join' => [
            'limit' => true,
            'title' => 'Подписываться на группу',
        ],
        'friends' => [
            'limit' => true,
            'title' => 'Добавляться в друзья',
        ],
        'likes' => [
            'limit' => true,
            'title' => 'Ставить лайки',
        ],
        'polls' => [
            'limit' => true,
            'title' => 'Участвовать в опросах',
        ],
        'reposts' => [
            'limit' => true,
            'title' => 'Делать репосты',
        ],
        'video' => [
            'limit' => false,
            'title' => 'Просмотры видео',
        ],
        'views' => [
            'limit' => false,
            'title' => 'Просмотры постов',
        ],
    ];

    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setLocation('/users/login');
        }

        $this->_bot = $this->factoryBot->bots->getByUserId($this->_application->User->userId, true);

        if (!$this->_request->post['action']->string('')) {
            if ($this->_bot === null) {
                $settings = json_decode(file_get_contents(\Service\Orders\Model_Config::$settings), true);

                $vars = [
                    'userId' => $this->_application->UserID,
                    'settings' => $settings,
                ];

                return $this->_response->setBody(STPL::Fetch('client/start', $vars));
            }


            if (!$this->_application->User->access_token) {
                return $this->_response->setBody(STPL::Fetch('client/token'));
            }
        }

        if ($this->_request->get['isBot']->int(0)) {
            $this->_bot->isActive = !$this->_bot->isActive;
            $this->factoryBot->bots->save($this->_bot);

            return $this->_response->setLocation('/bot');
        }
        $this->_application->Title->addStyles(['/css/material-switch.min.css']);

        return null;
    }

    public function actionGet()
    {
        $settings = json_decode(file_get_contents(\Service\Orders\Model_Config::$settings), true);

        $total = $totalKarma = $totalBalance = 0;
        $tasks = [
            'total' => [],
            'balance' => [],
            'karma' => [],
        ];

        /* @var Model_Notifications_Notification $notification */
        if (isset($this->_application->notifications['bot'])) {
            foreach ($this->_application->notifications['bot'] as $notification) {
                $notification->makeShadow();
                $notification->status = 1;
                $this->factoryUsers->notifications->save($notification);
            }
        }

        $total = 0;
        $query = $this->factoryTasks->users->query();
        $query->filter->fieldValue('userId', '=', $this->_application->UserID)
            ->fieldValue('isBot', '=', true)
            ->fieldValue('isDone', '=', true)
            ->fieldValue('isDoneDate', '>=', strtotime('today'));

        $it = $query->iterator();
        /** @var Model_Users_User $user */
        foreach ($it as $user) {
            if (!isset($tasks['total'][$user->type])) {
                $tasks['total'][$user->type] = 1;
            } else {
                $tasks['total'][$user->type]++;
            }
            $total++;
        }

        if ($this->_bot->isPro) {
            $this->factoryUsers->users->balance->setDate(strtotime('today'));
            $query = $this->factoryUsers->users->balance->query();
            $query->filter->fieldValue('userId', '=', $this->_application->User->userId)
                ->fieldValue('isBot', '=', true)
                ->fieldValue('dateCreate', '>=', strtotime('today'));

            $it = $query->iterator();
            $totalBalance = 0;
            /** @var Model_Users_Balances_Balance $balance */
            foreach ($it as $balance) {
                $totalBalance += $balance->balance;
            }

            $this->factoryUsers->users->karma->setDate(strtotime('today'));
            $query = $this->factoryUsers->users->karma->query();
            $query->filter->fieldValue('userId', '=', $this->_application->User->userId)
                ->fieldValue('isBot', '=', true)
                ->fieldValue('dateCreate', '>=', strtotime('today'));

            $it = $query->iterator();
            $totalKarma = 0;

            foreach ($it as $karma) {
                $totalKarma += $karma->karma;
            }
        }

        $vars = [
            'tasks' => $tasks,
            'app' => $this->_application,
            'bot' => $this->_bot,
            'types' => Model_Config::$botTypes,
            'fields' => $this->_fields,
            'settings' => $settings,
            'total' => $total,
            'totalBalance' => $totalBalance,
            'totalKarma' => $totalKarma,
            'userId' => $this->_application->User->userId
        ];

        return $this->_response->setBody(STPL::Fetch('client/default', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'free':
                return $this->_free();
            case 'token':
                return $this->_token();
            case 'saveIsBot':
                return $this->_saveIsBot();
        }

        return $this->_response->setStatus(HttpResponse::S4_METHOD_NOT_ALLOWED);
    }

    private function _free()
    {
        if ($this->_bot !== null) {
            return null;
        }

        $this->_bot = $this->factoryBot->bots->getNew();
        $this->_bot->userId = $this->_application->User->userId;
        $this->_bot->dateCreate = time();
        $this->_bot->isBot = array_sum(array_keys(Model_Config::$botTypes));
        $this->_bot->isActive = true;

        $this->factoryBot->bots->save($this->_bot);

        return null;
    }

    /*
     * Сохранение access_token
     * забираем access_token из ссылки указанной пользователем
     */
    private function _token()
    {
        $access_token = $this->_request->post['access_token']->string();

        if (strpos($access_token, '&') > 0) {
            $arr = explode('&', $access_token);

            foreach ($arr as $string) {
                if (preg_match('@access_token=(.*)@', $string, $matches)) {
                    $access_token = $matches[1];
                }
            }
        }

        if (preg_match('@access_token=(.*)@', $access_token, $matches)) {
            $access_token = $matches[1];
        }
        $this->_application->User->makeShadow();
        $this->_application->User->access_token = $access_token;
        $this->factoryUsers->users->save($this->_application->User);

        return $this->_response->setLocation('/bot');
    }

    /*
     * сохранение настроек бота (для про ботов)
     */
    private function _saveIsBot()
    {
        if (!$this->_bot->isPro) {
            return null;
        }

        $isBot = $this->_request->post['isBot']->asArray();
        $this->_bot->isBot = array_sum($isBot);

        $this->factoryBot->bots->save($this->_bot);

        return null;
    }
}
