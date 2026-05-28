<?php

namespace Service\Tasks;

class Controller_State_Client_Edit extends Controller_State_Client
{
    public function actionPrepare()
    {
        //проверяем авторизацию
        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setLocation('/users/login');
        }

        $this->_task = $this->factoryTasks->tasks->getById($this->_params['taskId'], true);

        //разрешаем редактировать только свои задания
        if ($this->_task->userId != $this->_application->UserID) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }

        $this->_application->Title->addScript('/js/tasks/edit.min.js');
        parent::actionPrepare();
    }

    public function actionGet()
    {

        $cities = array_reverse($this->factoryUsers->cities->getListByCount());
        $countries = array_reverse($this->factoryUsers->countries->getListByCount(false, 3));

        $vars = [
            'vkTypes' => $this->_vkTypes,
            'types' => [
                'likes',
                'reposts',
                'comments',
                'join',
                'polls',
                'views',
                'video',
            ],
            'cities' => $cities,
            'countries' => $countries,
            'task' => $this->_task,
            'user' => $this->_application->User,
            'action' => 'add',
            'errors' => $this->_errors,
            'prices' => [
                'likes' => $this->_application->settings['price_likes_buy'],
                'reposts' => $this->_application->settings['price_reposts_buy'],
                'comments' => $this->_application->settings['price_comments_buy'],
                'join' => $this->_application->settings['price_join_buy'],
                'polls' => $this->_application->settings['price_polls_buy'],
                'views' => $this->_application->settings['price_views_buy'],
                'video' => $this->_application->settings['price_video_buy'],
            ],
            'percents' => [
                'percent_sex' => floatval($this->_application->settings['percent_sex']),
                'percent_ageFrom' => floatval($this->_application->settings['percent_ageFrom']),
                'percent_ageTo' => floatval($this->_application->settings['percent_ageTo']),
                'percent_country' => floatval($this->_application->settings['percent_country']),
                'percent_city' => floatval($this->_application->settings['percent_city']),
                'percent_relation' => floatval($this->_application->settings['percent_relation']),
                'percent_avatarCount' => floatval($this->_application->settings['percent_avatarCount']),
                'percent_filled' => floatval($this->_application->settings['percent_filled']),
                'percent_pageAge' => floatval($this->_application->settings['percent_pageAge']),
                'percent_followersCount' => floatval($this->_application->settings['percent_followersCount']),
                'percent_interestingPage' => floatval($this->_application->settings['percent_interestingPage']),
                'percent_frequencyPost' => floatval($this->_application->settings['percent_frequencyPost']),
                'percent_prior' => floatval($this->_application->settings['percent_prior']),
            ],
            'app' => $this->_application,
        ];


        return $this->_response->setBody(\STPL::Fetch('client/edit', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'add':
                return $this->_edit();
        }

        return parent::actionPost();
    }
}
