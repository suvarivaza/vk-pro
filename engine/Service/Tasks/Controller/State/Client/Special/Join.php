<?php

namespace Service\Tasks;

class Controller_State_Client_Special_Join extends Controller_State_Client
{
    /** @var Model_Specials_Groups_Group */
    private $_group = null;

    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response != null) {
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
        $this->_task->url = $this->_group->url;
        $this->_task->ownerId = strval(-$this->_group->ownerId);
        $this->_task->itemId = '';
        $this->_task->title = $this->_group->title;
        $this->_task->isSpecial = true;
        $this->_task->isSpecialInvite = true;
        $this->_task->specialId = $this->_group->groupId;
        $this->_task->type = 'join';
        $this->_task->vkType = 'group';

        $this->_application->Title->addScript('/js/tasks/special.min.js?v=1.2');

        return null;
    }

    public function actionGet()
    {
        $cities = array_reverse($this->factoryUsers->cities->getListByCount());
        $countries = array_reverse($this->factoryUsers->countries->getListByCount(false, 3));
        $percentsVals = json_decode(file_get_contents(ENGINE_PATH . 'engine/Service/Tasks/Model/Config.json'), true);

        $vars = [
            'action' => 'save',
            'special' => $this->_group,
            'task' => $this->_task,
            'cities' => $cities,
            'countries' => $countries,
            'user' => $this->_application->User,
            'errors' => $this->_errors,
            'prices' => [
                'likes' => floatval($this->_application->settings['price_likes_buy']),
                'reposts' => floatval($this->_application->settings['price_reposts_buy']),
                'comments' => floatval($this->_application->settings['price_comments_buy']),
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

        return $this->_response->setBody(\STPL::Fetch('client/special/join', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'save':
                return $this->_save();
            case 'task_check':
                return $this->_task_check();
        }
    }

    private function _save()
    {
        $task = $this->_task_check_join($this->_task->url, $this->_task->type);

        if ($task !== null) {
            $task->makeShadow();

            if (!$task->isDel) {
                $sum = $task->price * $task->countRemain;
                $this->_application->User->makeShadow();
                $this->_application->User->balance += $sum;
                $this->factoryUsers->users->save($this->_application->User);
            }

            $this->_task = $task;
        }

        $prices = [
            'likes' => floatval($this->_application->settings['price_likes_buy']),
            'reposts' => floatval($this->_application->settings['price_reposts_buy']),
            'comments' => floatval($this->_application->settings['price_comments_buy']),
            'join' => floatval($this->_application->settings['price_join_buy']),
            'polls' => floatval($this->_application->settings['price_polls_buy']),
            'views' => floatval($this->_application->settings['price_views_buy']),
            'video' => floatval($this->_application->settings['price_video_buy']),
        ];
        $percents = [
            'percent_sex' => floatval($this->_application->settings['percent_sex']),
            'percent_ageFrom' => floatval($this->_application->settings['percent_ageFrom']),
            'percent_ageTo' => floatval($this->_application->settings['percent_ageTo']),
            'percent_country' => floatval($this->_application->settings['percent_country']),
            'percent_city' => floatval($this->_application->settings['percent_city']),
            'percent_city_my' => floatval($this->_application->settings['percent_city_ny']),
            'percent_relation' => floatval($this->_application->settings['percent_relation']),
            'percent_avatarCount' => floatval($this->_application->settings['percent_avatarCount']),
            'percent_filled' => floatval($this->_application->settings['percent_filled']),
            'percent_pageAge' => floatval($this->_application->settings['percent_pageAge']),
            'percent_followersCount' => floatval($this->_application->settings['percent_followersCount']),
            'percent_interestingPage' => floatval($this->_application->settings['percent_interestingPage']),
            'percent_frequencyPost' => floatval($this->_application->settings['percent_frequencyPost']),
            'percent_prior' => floatval($this->_application->settings['percent_prior']),
        ];
        $percentsVals = json_decode(file_get_contents(ENGINE_PATH . 'engine/Service/Tasks/Model/Config.json'), true);

        $this->_task->minKarma = $this->_request->post['minKarma']->int(0);
        $this->_task->followersOnly = $this->_request->post['followersOnly']->bool(false);
        $this->_task->newFollowers = $this->_request->post['newFollowers']->bool(false);
        $this->_task->prior = $this->_request->post['prior']->bool(false);
        $this->_task->count = $this->_request->post['count']->int(0);
        $this->_task->countReady = 0;
        $this->_task->countReadyBot = 0;
        $this->_task->countRemain = $this->_task->count - $this->_task->countReady;
        $this->_task->userId = $this->_application->UserID;
        $this->_task->targeting = $this->_request->post['targeting']->bool();
        $this->_task->sex = $this->_request->post['sex']->int(0);
        $this->_task->ageFrom = $this->_request->post['ageFrom']->int(0);
        $this->_task->ageTo = $this->_request->post['ageTo']->int(0);
        $this->_task->cityId = 0;
        $this->_task->countryId = 0;
        $this->_task->pollId = $this->_request->post['pollId']->int(0);
        $this->_task->answerId = $this->_request->post['answerId']->int(0);
        $this->_task->answerIds = $this->_request->post['answerIds']->string('');
        $this->_task->isSpecial = true;
        $this->_task->active = true;
        $this->_task->isDel = false;

        $this->_task->commentType = $this->_request->post['commentType']->enum(0, [0, 1, 2, 3],
            \System\HttpRequest::INTEGER_NUM);
        $this->_task->setComments($this->_request->post['comments']->asArray([], \System\HttpRequest::OUT_HTML_CLEAN));

        $cityId = $this->_request->post['cityId']->int(0);
        $country = $this->factoryUsers->countries->getById($cityId);

        if ($country !== null) {
            $this->_task->countryId = $country->countryId;
        } else {
            $city = $this->factoryUsers->cities->getById($cityId);

            if ($city !== null) {
                $this->_task->cityId = $city->cityId;
            }
        }
        $this->_task->relation = $this->_request->post['relation']->int(0);
        $this->_task->avatarCount = $this->_request->post['avatarCount']->int(0);
        $this->_task->filled = $this->_request->post['filled']->int(0);
        $this->_task->pageAge = $this->_request->post['pageAge']->int(0);
        $this->_task->followersCount = $this->_request->post['followersCount']->int(0);
        $this->_task->interestingPage = $this->_request->post['interestingPage']->int(0);
        $this->_task->frequencyPost = $this->_request->post['frequencyPost']->int(0);

        $photo = [
            'small' => $this->savePhoto($this->_group->photo),
            'big' => $this->savePhoto($this->_group->photo),
        ];
        $this->_task->setPhoto($photo);

        $price = $prices[$this->_task->type] * $this->_task->count;
        $sum = $price;

        if ($this->_task->sex > 0) {
            $val = $percentsVals['sex'][$this->_task->sex] ?: $percents['percent_sex'];
            $sum += $price * $val / 100;
        }

        if ($this->_task->ageFrom > 0) {
            $val = $percentsVals['ageFrom'][$this->_task->ageFrom] ?: $percents['percent_ageFrom'];
            $sum += $price * $val / 100;
        }

        if ($this->_task->ageTo > 0) {
            $val = $percentsVals['ageTo'][$this->_task->ageTo] ?: $percents['percent_ageTo'];
            $sum += $price * $val / 100;
        }

        if ($this->_task->prior && $percents['percent_prior'] > 0) {
            $sum += $price * ($percents['percent_prior'] / 100);
        }

        if ($this->_task->cityId == $this->_application->User->cityId) {
            $sum += $price * $percents['percent_city_my'] / 100;
        } elseif ($this->_task->cityId > 0) {
            $sum += $price * $percents['percent_city'] / 100;
        } elseif ($this->_task->countryId > 0) {
            $sum += $price * $percents['percent_country'] / 100;
        }

        if ($this->_task->relation > 0) {
            $val = $percentsVals['relation'][$this->_task->relation] ?: $percents['percent_relation'];
            $sum += $price * $val / 100;
        }

        if ($this->_task->avatarCount > 0) {
            $val = $percentsVals['avatarCount'][$this->_task->avatarCount] ?: $percents['percent_avatarCount'];
            $sum += $price * $val / 100;
        }

        if ($this->_task->filled > 0) {
            $val = $percentsVals['filled'][$this->_task->filled] ?: $percents['percent_filled'];
            $sum += $price * $val / 100;
        }

        if ($this->_task->pageAge > 0) {
            $val = $percentsVals['pageAge'][$this->_task->pageAge] ?: $percents['percent_pageAge'];
            $sum += $price * $val / 100;
        }

        if ($this->_task->followersCount > 0) {
            $val = $percentsVals['followersCount'][$this->_task->followersCount] ?: $percents['percent_followersCount'];
            $sum += $price * $val / 100;
        }

        if ($this->_task->interestingPage > 0) {
            $val = $percentsVals['interestingPage'][$this->_task->interestingPage] ?: $percents['percent_interestingPage'];
            $sum += $price * $val / 100;
        }

        if ($this->_task->frequencyPost > 0) {
            $val = $percentsVals['frequencyPost'][$this->_task->frequencyPost] ?: $percents['percent_frequencyPost'];
            $sum += $price * $val / 100;
        }

        if ($this->_task->minKarma > 0) {
            $val = $percentsVals['minKarma'][$this->_task->minKarma] ?: 0;
            $sum += $price * $val / 100;
        }

        if ($sum > $this->_application->User->balance) {
            $this->_errors[] = 'Недостаточно средств на счете. Пополните.';
        }

        if (count($this->_errors)) {
            return null;
        }
        $this->_task->price = $sum / $this->_task->count;
        $this->_task->sum = floatval($sum);

        if ($this->factoryTasks->tasks->save($this->_task)) {
            $this->_application->User->makeShadow();
            $this->_application->User->balance -= $sum;
            $this->factoryUsers->users->save($this->_application->User);

            return $this->_response->setLocation('/tasks/special/' . $this->_group->groupId . '/all/1');
        }

        return $this->_response->setStatus(\System\HttpResponse::S4_BAD_REQUEST);
    }

    /**
     * @param $url
     * @param $type
     */
    protected function _task_check_join($url, $type)
    {
        $query = $this->factoryTasks->tasks->query()->sqlCalcFoundRows(true)->limit(1)->sort('taskId', 'ASC');
        $query->filter->fieldValue('userId', '=', $this->_application->UserID)
            ->fieldValue('isSpecial', '=', true)
            ->fieldValue('url', '=', $url)
            ->fieldValue('type', '=', $type);

        $it = $query->iterator();
        $total = $it->getTotal();

        if ($total > 0) {
            $task = $it->current();

            return $task;
        }

        return null;
    }

    protected function _task_check()
    {

        $query = $this->factoryTasks->tasks->query()->sqlCalcFoundRows(true)->limit(1)->sort('taskId', 'ASC');
        $query->filter->fieldValue('userId', '=', $this->_application->UserID)
            ->fieldValue('isSpecial', '=', $this->_task->isSpecial)
            ->fieldValue('url', '=', $this->_task->url)
            ->fieldValue('type', '=', $this->_task->type);


        $it = $query->iterator();
        $total = $it->getTotal();

        if ($total > 0) {
            $task = $it->current();
            $vars = [
                'action' => 'save',
                'taskId' => $task->taskId,
            ];

            return $this->_response->setJson(['success' => false, 'html' => \STPL::Fetch('client/edit/check', $vars)]);
        }

        return $this->_response->setJson(['success' => true]);
    }
}
