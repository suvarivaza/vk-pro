<?php

namespace Service\Auto;

use Service\Users\Model_Notifications_Notification;
use STPL;
use System\HttpRequest;
use System\HttpResponse;

class Controller_State_Client_Default extends Controller_State_Client
{
    /** @var Model_Autos_Auto */
    protected $_auto = null;
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
        $isFree = $this->_request->post['isFree']->bool(false);
        $action = $this->_request->post['action']->string('');

        if ($this->_auto === null && !$isFree && $action != 'access_token') {
            $settings = json_decode(file_get_contents(\Service\Orders\Model_Config::$settings), true);

            $vars = [
                'userId' => $this->_application->UserID,
                'isFree' => !($this->_application->User->isFree & 2),
                'settings' => $settings,
            ];

            return $this->_response->setBody(STPL::Fetch('client/start', $vars));
        }

        $this->_application->Title->addScript('/js/tasks/edit.min.js');
        $this->_application->Title->addStyles(['/css/material-switch.min.css']);

        return null;
    }

    public function actionGet()
    {
        $groups = $this->factoryAuto->auto->groups->getByAutoId($this->_auto->autoId);

        /** @var Model_Notifications_Notification $notification */
        if(isset($this->_application->notifications['auto'])){
            foreach ($this->_application->notifications['auto'] as $notification) {
                $notification->makeShadow();
                $notification->status = 1;
                $this->factoryUsers->notifications->save($notification);
            }
        }


        $vars = [
            'auto' => $this->_auto,
            'groups' => $groups,
        ];

        return $this->_response->setBody(STPL::Fetch('client/default', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'getGroups':
                return $this->_getGroups();
            case 'access_token':
                return $this->_access_token();
            case 'setGroup':
                return $this->_setGroup();
            case 'getGroupForm':
                return $this->_getGroupForm();
            case 'getTemplateFrom':
                return $this->_getTemplateFrom();
            case 'templateAdd':
                return $this->_templateAdd();
            case 'getTemplateList':
                return $this->_getTemplateList();
        }

        return $this->_response->setStatus(HttpResponse::S4_METHOD_NOT_ALLOWED);
    }

    protected function _getGroups()
    {
        if (!$this->_application->User->access_token || ($this->_application->User->access_token_expire != null && $this->_application->User->access_token_expire < time())) {
            return $this->_response->setJson([
                'success' => true,
                'token' => true,
                'html' => STPL::Fetch('client/token'),
            ]);
        }
        $isFree = $this->_request->post['isFree']->bool(false);

        if (!$isFree) {
            $slots = $this->_auto->getSlots();

            if (!count($slots)) {
                $vars = [
                    'userId' => $this->_application->UserID,
                    'settings' => json_decode(file_get_contents(\Service\Orders\Model_Config::$settings), true),
                ];

                return $this->_response->setJson([
                    'success' => true,
                    'html' => STPL::Fetch('client/form_group_buy', $vars),
                ]);
            }
        }


        $groups = $this->VK->getGroups($this->_application->User->uid, $this->_application->User->access_token);

        if (!isset($groups['items'])) {
            return $this->_response->setJson([
                'success' => true,
                'token' => true,
                'html' => STPL::Fetch('client/token'),
            ]);
        }

        foreach ($groups['items'] as $group) {
            $_SESSION['groups'][$group['id']] = $group;
        }

        $vars = [
            'months' => $this->_request->post['months']->int(0),
            'groups' => $groups,
            'group' => $this->_group,
        ];

        if ($isFree) {
            $vars['isFree'] = true;
        }

        return $this->_response->setJson(['success' => true, 'html' => STPL::Fetch('client/groups', $vars)]);
    }

    protected function _access_token()
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

        return $this->_getGroups();
    }

    protected function _setGroup()
    {
        $isFree = $this->_request->post['isFree']->bool(false);

        if ($isFree && $this->_application->User->isFree & 2) {
            return $this->_response->setStatus(HttpResponse::S4_FORBIDDEN)->setJson([
                'success' => false,
                'errorText' => 'Вы уже использовали бесплатный период',
            ]);
        }

        if ($isFree && !$this->_auto) {
            $this->_auto = $this->factoryAuto->auto->getNew();
            $this->_auto->userId = $this->_application->UserID;
            $this->_auto->dateCreate = time();
            $this->_auto->isActive = true;
            $this->_auto->dateValid += strtotime('+1 MONTH');
            $slots = [];
            $this->_auto->setSlots($slots);
            $this->factoryAuto->auto->save($this->_auto);
        }

        $months = $this->_request->post['months']->int(0);

        $this->_auto->makeShadow();

        if (!$isFree) {
            $slots = $this->_auto->getSlots();
            $slot = null;

            if ($months > 0) {
                foreach ($slots as $id => $slot) {
                    if ($slot == $months) {
                        unset($slots[$id]);
                        break;
                    }
                }
            } else {
                $slot = array_shift($slots);
            }

            if (!$slot) {
                $vars = [
                    'settings' => json_decode(file_get_contents(\Service\Orders\Model_Config::$settings), true),
                ];

                return $this->_response->setJson([
                    'success' => true,
                    'html' => STPL::Fetch('client/form_group_buy', $vars),
                ]);
            }
            $this->_auto->setSlots($slots);
        } else {
            $slot = 1;
        }

        $groupId = $this->_request->post['groupId']->int();

        if (!isset($_SESSION['groups'][$groupId])) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => 'Необходимо указать группу, для которой вы являетесь владельцем',
            ]);
        }

        $item = $_SESSION['groups'][$groupId];

        $query = $this->factoryAuto->auto->groups->query();
        $query->filter->fieldValue('ownerId', '=', intval($item['id']));
        $it = $query->iteratorForSave();
        /** @var Model_Autos_Groups_Group $group */
        $group = $it->current();

        if ($group) {
            if ($group->wasFree && $isFree) {
                return $this->_response->setJson([
                    'success' => false,
                    'errorText' => 'Данная группа участвовала в пробном режиме',
                ]);
            }

            if ($group->userId != $this->_application->UserID && !$this->_request->post['take']->bool(false)) {
                $user = $this->factoryUsers->users->getById($group->userId);

                return $this->_response->setJson([
                    'success' => false,
                    'errorText' => '<h5 class="text-center">Данная группа активирована пользователем <strong>' . $user->name . '</strong>.</h5><div class="text-center"><button onclick="auto.saveClick(true);" class="btn btn-success">Перенести группу</button></div>',
                ]);
            }

            if ($group->userId != $this->_application->UserID) {
                $this->factoryAuto->auto->groups->delete($group);
                $group = $this->factoryAuto->auto->groups->getNew();
                $group->title = $item['name'];
                $group->photo = $item['photo_50'];
                $group->ownerId = strval($item['id']);
                $group->autoId = $this->factoryAuto->auto->getByUserIdIsActive($this->_application->UserID,
                    true)->autoId;
                $group->userId = $this->_application->UserID;
                $group->dateValid = strtotime('+' . $slot . ' MONTH');
                $group->url = 'https://vk.com/' . $item['screen_name'];
            }

            if ($group->isFree && !$isFree) {
                $group->isFree = false;
                $group->isFreeCount = 0;
                $group->dateValid = strtotime('+' . $slot . ' MONTH', time());
            } else {
                $group->dateValid = strtotime('+' . $slot . ' MONTH', max($group->dateValid, time()));
            }

            if ($this->factoryAuto->auto->groups->save($group)) {
                $query = $this->factoryUsers->notifications->query();
                $query->filter->fieldValue('userId', '=', $this->_application->UserID);
                $query->filter->fieldValue('service', '=', 'auto');
                $query->filter->fieldValue('objectId', '=', $group->autoGroupId);
                $it = $query->iteratorForSave();

                foreach ($it as $notification) {
                    $notification->status = 2;
                    $this->factoryUsers->notifications->save($notification);
                }
                $this->factoryAuto->auto->save($this->_auto);
            }

            return $this->_response->setJson(['success' => true, 'reload' => true]);
        }

        $group = $this->factoryAuto->auto->groups->getNew();
        $group->title = $item['name'];
        $group->photo = $item['photo_50'];
        $group->ownerId = intval($item['id']);
        $group->autoId = $this->factoryAuto->auto->getByUserIdIsActive($this->_application->UserID, true)->autoId;
        $group->userId = $this->_application->UserID;
        $group->dateValid = strtotime('+' . $slot . ' MONTH');
        $group->url = 'https://vk.com/' . $item['screen_name'];

        if ($isFree) {
            $config = \Service\Orders\Model_Config::getSettings();
            $group->isFree = true;
            $group->wasFree = true;
            $group->isFreeCount = intval($config['auto']['free']);
        }


        $response = $this->VK->getCallbackConfirmationCode($group->ownerId, $this->_application->User->access_token);


        if (!$response['code']) {
            return $this->_response->setJson([
                'success' => true,
                'token' => true,
                'html' => STPL::Fetch('client/token'),
            ]);
        }
        $group->code = $response['code'];

        if ($this->factoryAuto->auto->groups->save($group)) {

            $response = $this->VK->addCallbackServer($group->ownerId, $this->_application->User->access_token);

            if ($response['error'] != 0) {
                $this->factoryAuto->auto->groups->delete($group);

                return $this->_response->setJson([
                    'success' => false,
                    'errorText' => 'Не удалось подписаться на группу',
                ]);
            }
            unset($_SESSION['groups']);


            $response = $this->VK->setCallbackSettings($group->ownerId, $this->_application->User->access_token);


            $this->factoryAuto->auto->save($this->_auto);

            if ($isFree) {
                $this->_application->User->makeShadow();
                $this->_application->User->isFree = $this->_application->User->isFree | 2;
                $this->factoryUsers->users->save($this->_application->User);
            }

            return $this->_response->setJson(['success' => true, 'reload' => true]);
        }

        return $this->_response->setJson(['success' => false, 'errorText' => 'Ошибка сервера']);
    }

    protected function _getGroupForm()
    {
        $this->_group = $this->factoryAuto->auto->groups->getById($this->_request->post['groupId']);

        if ($this->_group === null) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Группа не найдена']);
        }

        if ($this->_group->autoId != $this->_auto->autoId) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Группа не найдена']);
        }

        $templates = $this->factoryAuto->auto->templates->getByGroupId($this->_group->autoGroupId);

        $vars = [
            'auto' => $this->_auto,
            'group' => $this->_group,
            'templates' => $templates,
        ];

        return $this->_response->setJson(['success' => true, 'html' => STPL::Fetch('client/group/detail', $vars)]);
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

        $this->_template = $this->factoryAuto->auto->templates->getNew();
        $special = $this->factoryTasks->specialGroups->getByOwnerId($group->ownerId);

        if ($special) {
            $this->_template->specialId = $special->specialId;
        }
        $cities = array_reverse($this->factoryUsers->cities->getListByCount());
        $countries = array_reverse($this->factoryUsers->countries->getListByCount(false, 3));
        $percentsVals = json_decode(file_get_contents(ENGINE_PATH . 'engine/Service/Tasks/Model/Config.json'), true);

        $sets = json_decode(file_get_contents(Model_Config::$settings), true);
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
        $template = $this->factoryAuto->auto->templates->getNew();
        $template->userId = $this->_application->UserID;
        $template->groupId = $this->_request->post['groupId']->int(0);
        $template->autoId = $this->_auto->autoId;
        $template->type = $this->_request->post['type']->enum('', ['likes', 'reposts', 'polls', 'comments', 'views']);

        if (!$template->type) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Укажите тип заданий']);
        }
        $template->minKarma = intval($this->_request->post['minKarma']->enum(0, [25, 50, 75]));
        $template->prior = $this->_request->post['prior']->bool();
        $template->fromGroupOnly = $this->_request->post['fromGroupOnly']->bool();
        $attachmentTypes = $this->_request->post['attachmentType']->asArray([]);
        $attachmentType = 0;

        foreach ($attachmentTypes as $val) {
            $attachmentType |= $val;
        }
        $template->attachmentType = intval($attachmentType);
        $template->adsOut = $this->_request->post['adsOut']->bool();
        $template->title = $this->_request->post['title']->string('', HttpRequest::OUT_HTML);
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
        $groupId = $this->_request->post['groupId']->int(0);
        $group = $this->factoryAuto->auto->groups->getById($groupId);

        if ($group === null) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Группа не найдена']);
        }

        $query = $this->factoryAuto->auto->templates->query()->sort('dateCreate', 'DESC');
        $query->filter->fieldValue('groupId', '=', $group->autoGroupId);
        $query->filter->fieldValue('userId', '=', $this->_application->UserID);
        $query->filter->fieldValue('autoId', '=', $this->_auto->autoId);

        $it = $query->iterator();

        $vars = [
            'list' => $it,
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
}
