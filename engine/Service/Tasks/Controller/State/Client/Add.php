<?php

namespace Service\Tasks;

class Controller_State_Client_Add extends Controller_State_Client
{
    public function actionPrepare()
    {
        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setLocation('/users/login');
        }

        $this->_task = $this->factoryTasks->tasks->getNew();
        $this->_task->type = 'likes';


        //$this->_application->Title->addScript('/js/tasks/edit.min.js');

        $this->_application->Title->addScripts(['/js/tasks/edit.js?' . filemtime(VAR_PATH . 'js/tasks/edit.js')]);


        $this->_application->Title->add('link', [
            'rel' => 'icon',
            'href' => '/img/icons/32/icon-create.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->add('link', [
            'rel' => 'shortcut icon',
            'href' => '/img/icons/32/icon-create.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->Title = 'Создать задание';

        parent::actionPrepare();
    }

    public function actionGet()
    {
        $cities = array_reverse($this->factoryUsers->cities->getListByCount());
        $countries = array_reverse($this->factoryUsers->countries->getListByCount(false, 3));
        $percentsVals = json_decode(file_get_contents(ENGINE_PATH . 'engine/Service/Tasks/Model/Config.json'), true);
        $vars = [
            'types' => [
                'likes',
                'reposts',
                'comments',
                'join',
                'friends',
                'polls',
                'views',
                'video',
            ],
            'vkTypes' => $this->_vkTypes,
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
            'app' => $this->_application,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/edit_new', $vars));
    }

    public function actionPost()
    {

        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'add':
                return $this->_edit();
            case 'task_check':
                return $this->_double_task_check();
            case 'countUsers':
                return $this->_countUsers();
        }

        return parent::actionPost();
    }

    private function _countUsers()
    {
        $sex = $this->_request->post['sex']->int(0);
        $ageFrom = $this->_request->post['ageFrom']->int(0);
        $ageTo = $this->_request->post['ageTo']->int(0);
        $city = $this->_request->post['city']->int(0);
        $relation = $this->_request->post['relation']->int(0);
        $avatarCount = $this->_request->post['avatarCount']->int(0);
        $filled = $this->_request->post['filled']->int(0);
        $pageAge = $this->_request->post['pageAge']->int(0);
        $followersCount = $this->_request->post['followersCount']->int(0);
        $interestingPage = $this->_request->post['interestingPage']->int(0);
        $frequencyPost = $this->_request->post['frequencyPost']->int(0);
        $karma = $this->_request->post['karma']->int(0);

        $count = $this->factoryUsers->users->getCountTargeting(
            $sex,
            $ageFrom,
            $ageTo,
            $city,
            $relation,
            $avatarCount,
            $filled,
            $pageAge,
            $followersCount,
            $interestingPage,
            $frequencyPost,
            $karma
        );

        if ($count !== null) {
            return $this->_response->setJson(['success' => true, 'userCount' => ceil($count / 3)]);
        }

        return $this->_response->setJson(['success' => false]);
    }
}
