<?php

namespace Service\Tasks;

class Controller_State_Client_Special_Add extends Controller_State_Client
{
    /** @var Model_Specials_Groups_Group */
    private $_group = null;

    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setLocation('/users/login');
        }

        if ($this->_application->User->login == '') {
            return $this->_response->setLocation('/users/register');
        }

        $this->_group = $this->factoryTasks->specialGroups->getById($this->_params['specialId']);

        if ($this->_group === null) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }

        if ($this->_group->userId != $this->_application->UserID) {
            return $this->_response->setStatus(\System\HttpResponse::S4_FORBIDDEN);
        }

        $this->_task = $this->factoryTasks->tasks->getNew();
        $this->_task->userId = $this->_application->UserID;
        $this->_task->title = $this->_group->title;
        $this->_task->isSpecial = true;
        $this->_task->specialId = $this->_group->groupId;
        $this->_task->type = 'likes';

        $this->_application->Title->addScript('/js/tasks/edit.min.js?v=1.1');

        return null;
    }

    public function actionGet()
    {
        $tips = json_decode(file_get_contents(Model_Config::$specialTips), true);
        $cities = array_reverse($this->factoryUsers->cities->getListByCount());
        $countries = array_reverse($this->factoryUsers->countries->getListByCount(false, 3));
        $percentsVals = json_decode(file_get_contents(ENGINE_PATH . 'engine/Service/Tasks/Model/Config.json'), true);
        $vars = [
            'vkTypes' => $this->_vkTypes,
            'group' => $this->_group,
            'types' => [
                'likes',
                'reposts',
                'comments',
                'join',
                'polls',
                'views',
                'video',
            ],
            'request' => $this->_request,
            'cities' => $cities,
            'countries' => $countries,
            'task' => $this->_task,
            'user' => $this->_application->User,
            'action' => 'add',
            'errors' => $this->_errors,
            'prices' => [
                'likes' => floatval($this->_application->settings['price_likes_buy']),
                'reposts' => floatval($this->_application->settings['price_reposts_buy']),
                'comments' => floatval($this->_application->settings['price_comments_buy']),
                'friends' => floatval($this->_application->settings['price_friends_buy']),
                'join' => floatval($this->_application->settings['price_join_buy']),
                'polls' => floatval($this->_application->settings['price_polls_buy']),
                'views' => floatval($this->_application->settings['price_views_buy']),
                'video' => floatval($this->_application->settings['price_video_buy']),
            ],
            'percents' => [
                'percent_sex' => floatval($this->_application->settings['percent_sex']),
                'percent_ageFrom' => floatval($this->_application->settings['percent_ageFrom']),
                'percent_ageTo' => floatval($this->_application->settings['percent_ageTo']),
                'percent_country' => floatval($this->_application->settings['percent_country']),
                'percent_city' => floatval($this->_application->settings['percent_city']),
                'percent_city_my' => floatval($this->_application->settings['percent_city_my']),
                'percent_relation' => floatval($this->_application->settings['percent_relation']),
                'percent_avatarCount' => floatval($this->_application->settings['percent_avatarCount']),
                'percent_filled' => floatval($this->_application->settings['percent_filled']),
                'percent_pageAge' => floatval($this->_application->settings['percent_pageAge']),
                'percent_followersCount' => floatval($this->_application->settings['percent_followersCount']),
                'percent_interestingPage' => floatval($this->_application->settings['percent_interestingPage']),
                'percent_frequencyPost' => floatval($this->_application->settings['percent_frequencyPost']),
                'percent_prior' => floatval($this->_application->settings['percent_prior']),
            ],
            'percentsVals' => $percentsVals,
            'tips' => $tips,
            'app' => $this->_application,
            'total' => $this->factoryTasks->specialGroups->users->getCountTotal($this->_group->groupId),
        ];

        return $this->_response->setBody(\STPL::Fetch('client/special/edit', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();
        $response = null;

        switch ($action) {
            case 'add':
                $response = $this->_edit();
                break;
            case 'get_poll':
                return $this->_get_poll();
            case 'get_video':
                return $this->_get_video();
            case 'checkUrl':
                return $this->_checkUrl();
            case 'task_check':
                return $this->_double_task_check();
        }


        if ($response !== null) {
            if ($response->getStatus() == \System\HttpResponse::S3_FOUND) {
                $response->setLocation('/tasks/special/' . $this->_group->groupId . '/all/1');
            }
        }

        return $response;
    }
}
