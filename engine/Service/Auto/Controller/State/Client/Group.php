<?php

namespace Service\Auto;

use Service\Users\Model_Cities_City;
use STPL;
use System\HttpRequest;
use System\HttpResponse;

class Controller_State_Client_Group extends Controller_State_Client_Default
{
    /** @var Model_Autos_Auto */
    protected $_auto = null;
    /** @var Model_Autos_Groups_Group */
    protected $_group = null;
    /** @var Model_Autos_Templates_Template */
    protected $_template = null;

    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setLocation('/users/login');
        }

        $this->_application->Title->addScript('/js/jquery/jquery.dd.min.js');
        $this->_application->Title->addStyle('/css/jquery/dd.min.css');

        $this->_auto = $this->factoryAuto->auto->getByUserIdIsActive($this->_application->User->userId, true);

        if ($this->_auto === null) {
            return $this->_response->setLocation('/auto');
        }

        $this->_group = $this->factoryAuto->auto->groups->getById($this->_params['groupId']);

        if ($this->_group === null) {
            return $this->_response->setStatus(HttpResponse::S4_NOT_FOUND);
        }

        if ($this->_group->autoId != $this->_auto->autoId) {
            return $this->_response->setStatus(HttpResponse::S4_NOT_FOUND);
        }

        $this->_application->Title->addScript('/js/tasks/edit.min.js');
        $this->_application->Title->addStyles(['/css/material-switch.min.css']);

        return null;
    }

    public function actionGet()
    {
        $templates = $this->factoryAuto->auto->templates->getByGroupId($this->_group->autoGroupId);

        $vars = [
            'auto' => $this->_auto,
            'group' => $this->_group,
            'templates' => $templates,
            'template' => $this->factoryAuto->auto->templates->getNew(),
        ];

        return $this->_response->setBody(STPL::Fetch('client/group', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'getTemplateFrom':
                return $this->_getTemplateFrom();
            case 'templateAdd':
                return $this->_templateAdd();
            case 'getTemplateList':
                return $this->_getTemplateList();
            case 'templateToArchive':
                return $this->_templateToArchive();
            case 'templateToggle':
                return $this->_templateToggle();
            case 'getGroups':
                return $this->_getGroups();
        }

        return parent::actionPost();
    }

    protected function _getTemplateFrom()
    {
        $group = $this->factoryAuto->auto->groups->getById($this->_request->post['groupId']->int(0), true);

        if ($group === null) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Группа не найдена']);
        }

        if ($group->userId != $this->_application->User->userId) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Группа не найдена']);
        }
        $templateId = $this->_request->post['templateId']->int(0);

        if ($templateId > 0) {
            $this->_template = $this->factoryAuto->auto->templates->getById($templateId);

            if ($this->_template === null || $this->_template->userId != $this->_application->UserID) {
                return $this->_response->setJson(['success' => false, 'errorText' => 'Шаблон не найден']);
            }
        } else {
            $this->_template = $this->factoryAuto->auto->templates->getNew();
        }

        $special = $this->factoryTasks->specialGroups->getByOwnerId($group->ownerId);

        $cities = array_reverse($this->factoryUsers->cities->getListByCount());
        $countries = array_reverse($this->factoryUsers->countries->getListByCount(false, 3));
        $percentsVals = json_decode(file_get_contents(ENGINE_PATH . 'engine/Service/Tasks/Model/Config.json'), true);

        $sets = json_decode(file_get_contents(Model_Config::$settings), true);
        $this->_application->settings = array_merge($this->_application->settings, $sets['tips']);

        $vars = [
            'action' => 'templateAdd',
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
            'special' => $special,
            'groupId' => $this->_request->post['groupId']->int(0),
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
            'template' => $this->_template,
            'app' => $this->_application,
        ];

        return $this->_response->setJson([
            'success' => true,
            'html' => STPL::Fetch('client/group/templates/edit', $vars),
        ]);
    }

    protected function _templateAdd()
    {
        $templateId = $this->_request->post['templateId']->int(0);

        if ($templateId > 0) {
            $template = $this->factoryAuto->auto->templates->getById($templateId, true);

            if ($template === null || $template->userId != $this->_application->UserID) {
                return $this->_response->setJson(['success' => false, 'errorText' => 'Шаблон не найден']);
            }
        } else {
            $template = $this->factoryAuto->auto->templates->getNew();
        }

        $template->userId = $this->_application->UserID;
        $template->groupId = $this->_request->post['groupId']->int(0);
        $template->autoId = $this->_auto->autoId;
        $template->type = $this->_request->post['type']->enum('',
            ['likes', 'reposts', 'polls', 'comments', 'views', 'video']);

        if (!$template->type) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Укажите тип заданий']);
        }
        $template->minKarma = intval($this->_request->post['minKarma']->enum(0, [50, 75]));
        $template->prior = $this->_request->post['prior']->bool();
        $template->fromGroupOnly = $this->_request->post['fromGroupOnly']->bool();
        $attachmentTypes = $this->_request->post['attachmentType']->asArray([]);
        $attachmentType = 0;

        foreach ($attachmentTypes as $val) {
            $attachmentType |= $val;
        }
        $template->attachmentType = intval($attachmentType);
        $specialId = $this->_request->post['specialId']->int(0);

        if ($specialId) {
            $special = $this->factoryTasks->specials->getById($specialId);

            if ($special !== null && $special->userId == $this->_application->UserID) {
                $template->specialId = $special->specialId;
            }
        } else {
            $template->specialId = 0;
        }

        $template->adsOut = $this->_request->post['adsOut']->bool();
        $template->title = $this->_request->post['title']->string('', HttpRequest::OUT_HTML);

        if (!$template->title) {
            $this->_errors[] = 'Укажите наименование задания';
        }
        $template->dateCreate = time();
        $template->dateValid = strtotime('+1 MONTH');
        $template->isActive = true;
        $template->sex = $this->_request->post['sex']->int(0);
        $template->ageFrom = $this->_request->post['ageFrom']->int(0);
        $template->ageTo = $this->_request->post['ageTo']->int(0);
        $template->cityId = $this->_request->post['cityId']->int(0);
        $template->countryId = $this->_request->post['countryId']->int(0);
        $template->relation = $this->_request->post['relation']->int(0);
        $template->avatarCount = $this->_request->post['avatarCount']->int(0);
        $template->filled = $this->_request->post['filled']->int(0);
        $template->pageAge = $this->_request->post['pageAge']->int(0);
        $template->followersCount = $this->_request->post['followersCount']->int(0);
        $template->interestingPage = $this->_request->post['interestingPage']->int(0);
        $template->frequencyPost = $this->_request->post['frequencyPost']->int(0);
        $template->targeting = $this->_request->post['targeting']->bool();
        $template->setComments($this->_request->post['comments']->asArray());
        $template->commentType = $this->_request->post['commentType']->int(0);
        $template->price = 0.0;

        $template->hourMax = $this->_request->post['hourMax']->int(0);
        $template->hourFrom = $this->_request->post['hourFrom']->int(0);
        $template->hourTo = $this->_request->post['hourTo']->int(0);
        $template->weekDay = $this->_request->post['weekDay']->int(0);

        if (!$template->weekDay) {
            $this->_errors[] = 'Укажите периодичность запуска шаблона';
        }

        if ($template->weekDay == 8) {
            $beginOfDay = strtotime('midnight');
            $hour = date('H');

            if ($hour > $template->hourTo) {
                $template->weekDate = $beginOfDay;
            } else {
                $template->weekDate = strtotime('tomorrow', $beginOfDay);
            }
        }

        $template->balanceLimit = $this->_request->post['balanceLimit']->dec(0.0);
        $template->balanceRemain = 0.0;

        $template->countFrom = $this->_request->post['countFrom']->int(0);
        $template->countTo = $this->_request->post['countTo']->int(0);

        if (!$template->countTo) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => 'Укажите максимальное число выполнения задания',
            ]);
        }


        if (!empty($this->_errors) and count($this->_errors)) {
            return $this->_response->setJson(['success' => false, 'errorText' => implode(', ', $this->_errors)]);
        }


        if ($this->factoryAuto->auto->templates->save($template)) {
            return $this->_response->setJson(['success' => true]);
        }

        return $this->_response->setJson([
            'success' => false,
            'errorText' => 'Ошибка при сохранении шаблона. Попробуйте позднее',
        ]);
    }

    protected function _getTemplateList()
    {
        $isArchive = $this->_request->post['isArchive']->bool(false);
        $groupId = $this->_request->post['groupId']->int(0);
        $group = $this->factoryAuto->auto->groups->getById($groupId);

        if ($group === null) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Группа не найдена']);
        }

        $query = $this->factoryAuto->auto->templates->query()->sort('dateCreate', 'DESC');
        $query->filter->fieldValue('groupId', '=', $group->autoGroupId);
        $query->filter->fieldValue('userId', '=', $this->_application->UserID);
        $query->filter->fieldValue('autoId', '=', $this->_auto->autoId);

        $query->filter->fieldValue('isArchive', '=', $isArchive);
        /** @var Model_Cities_City[] $list */
        $list = array_reverse($this->factoryUsers->cities->getListByCount());
        $cities = [];
        $countries = [];

        foreach ($list as $city) {
            $cities[$city->cityId] = $city;
        }
        $list = array_reverse($this->factoryUsers->countries->getListByCount(false, 3));

        foreach ($list as $country) {
            $countries[$country->countryId] = $country;
        }

        $it = $query->iterator();
        $list = [];

        foreach ($it as $item) {
            $list[] = $item;
        }
        $vars = [
            'list' => $list,
            'isArchive' => $isArchive,
            'cities' => $cities,
            'countries' => $countries,
            'types' => [
                'likes' => 'Лайки',
                'reposts' => 'Репосты',
                'polls' => 'Голосования',
                'comments' => 'Комментарии',
                'views' => 'Просмотры',
                'video' => 'Просмотры видео',
            ],
            'attachmentType' => [
                0 => 'Все',
                2 => 'Аудиозапись',
                4 => 'Видеозапись',
                8 => 'Документ',
                16 => 'Изображение',
                32 => 'Опрос',
                64 => 'Текст',
            ],
            'weekDay' => [
                1 => 'Понедельник',
                2 => 'Вторник',
                3 => 'Среда',
                4 => 'Четверг',
                5 => 'Пятница',
                6 => 'Суббота',
                7 => 'Воскресенье',
                8 => 'Единоразово',
                9 => 'Ежедневно',
            ],
            'targeting' => [
                'sex' => [
                    0 => 'и парни и девушки',
                    2 => 'только парни',
                    1 => 'только девушки',
                ],
                'relation' => [
                    0 => 'любой',
                    1 => 'не женат/не замужем',
                    2 => 'есть друг/есть подруга',
                    3 => 'помолвлен/помолвлена',
                    4 => 'женат/замужем',
                    5 => 'всё сложно',
                    6 => 'в активном поиске',
                    7 => 'влюблён/влюблена',
                ],
                'avatarCount' => [
                    0 => 'любое',
                    1 => 'не менее 1',
                    2 => 'не менее 2',
                    5 => 'не менее 5',
                    10 => 'не менее 10',
                    20 => 'не менее 20',
                    50 => 'не менее 50',
                    100 => 'не менее 100',
                ],
                'filled' => [
                    0 => 'Любая',
                    1 => 'Не менее 1 раздела',
                    2 => 'Не менее 2 разделов',
                    3 => 'Не менее 3 разделов',
                    4 => 'Не менее 4 разделов',
                ],
                'pageAge' => [
                    0 => 'Любой',
                    1 => 'Не менее 3 месяцев',
                    2 => 'Не менее полугода',
                    3 => 'Не менее 1 года',
                    4 => 'Не менее 2 лет',
                    5 => 'Не менее 3 лет',
                ],
                'followersCount' => [
                    0 => 'Любое',
                    10 => 'Не менее 10',
                    50 => 'Не менее 50',
                    100 => 'Не менее 100',
                    200 => 'Не менее 200',
                    500 => 'Не менее 500',
                    1000 => 'Не менее 1 000',
                    5000 => 'Не менее 5 000',
                    10000 => 'Не менее 10 000',
                    20000 => 'Не менее 20 000',
                ],
                'interestingPage' => [
                    0 => 'Любое',
                    5 => 'Не более 5',
                    10 => 'Не более 10',
                    20 => 'Не более 20',
                    50 => 'Не более 50',
                    100 => 'Не более 100',
                    200 => 'Не более 200',
                    500 => 'Не более 500',
                    1000 => 'Не более 1 000',
                    2000 => 'Не более 2 000',
                ],
                'frequencyPost' => [
                    0 => 'Любая',
                    1 => 'Не более 1 поста в день',
                    2 => 'Не более 2 постов в день',
                    5 => 'Не более 5 постов в день',
                    10 => 'Не более 10 постов в день',
                    20 => 'Не более 20 постов в день',
                    50 => 'Не более 50 постов в день',
                    100 => 'Не более 100 постов в день',
                ],
            ],
        ];

        return $this->_response->setJson([
            'success' => true,
            'html' => STPL::Fetch('client/group/templates/list', $vars),
        ]);
    }

    protected function _templateToArchive()
    {
        $templateId = $this->_request->post['templateId']->int(0);

        if (!$templateId) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Не удалось найти шаблон']);
        }
        $template = $this->factoryAuto->auto->templates->getById($templateId, true);

        if ($template === null) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Не удалось найти шаблон']);
        }

        if ($template->userId != $this->_application->UserID) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Не удалось найти шаблон']);
        }

        if ($template->isArchive) {
            $template->isArchive = false;
        } else {
            $template->isArchive = true;
        }
        $template->isActive = false;

        $this->factoryAuto->auto->templates->save($template);

        return $this->_response->setJson(['success' => true]);
    }

    protected function _templateToggle()
    {
        $templateId = $this->_request->post['templateId']->int(0);

        if (!$templateId) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Не удалось найти шаблон']);
        }
        $template = $this->factoryAuto->auto->templates->getById($templateId, true);

        if ($template === null) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Не удалось найти шаблон']);
        }

        if ($template->userId != $this->_application->UserID) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Не удалось найти шаблон']);
        }

        $template->isActive = !$template->isActive;
        $this->factoryAuto->auto->templates->save($template);

        return $this->_response->setJson(['success' => true]);
    }
}
