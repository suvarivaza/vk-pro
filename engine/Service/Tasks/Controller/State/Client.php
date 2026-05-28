<?php

namespace Service\Tasks;

/**
 * Class Controller_State_Client
 *
 * @package Service\Tasks
 *
 * @property Model_Tasks_Task $_task
 */
abstract class Controller_State_Client extends \System\Service_Controller_State
{
    protected $_titles = [
        'likes' => [
            'title' => 'Поставить лайк',
            'vkTypes' => [
                'post' => 'Лайкнуть запись на стене',
                'photo' => 'Лайкнуть фотографию',
                'video' => 'Лайкнуть видеозапись',
                'comment' => 'Лайкнуть комментарий',
            ],
        ],
        'reposts' => [
            'title' => 'Сделать репост',
            'vkTypes' => [
                'post' => 'Репостнуть запись на стене',
                'photo' => 'Репостнуть фотографию',
                'video' => 'Репостнуть видеозапись',
                'comment' => 'Репостнуть комментарий',
            ],
        ],
        'comments' => [
            'title' => 'Оставить комментарий',
        ],
        'join' => [
            'title' => 'Подписаться',
        ],
        'friends' => [
            'title' => 'Добавить в друзья',
        ],
        'polls' => [
            'title' => 'Участвовать в опросе',
        ],
        'views' => [
            'title' => 'Просмотреть запись на стене',
        ],
        'video' => [
            'title' => 'Просмотреть видео',
        ],
    ];

    protected $_types = [
        'likes' => 'Поставить лайк',
        'reposts' => 'Сделать репост',
        'comments' => 'Оставить комментарий',
        'join' => 'Подписаться',
        'friends' => 'Добавить в друзья',
        'polls' => 'Участвовать в опросе',
        'views' => 'Просмотреть запись',
        'video' => 'Просмотреть видео',
    ];

    protected $_vkTypes = [
        'post' => 'Запись на стене',
        'photo' => 'Фотография',
        'video' => 'Видеозапись',
        'comment' => 'Комментарий',
    ];

    protected $_task = null;
    private $_limits = null;

    public function actionPrepare()
    {

        parent::actionPrepare();
    }

    public function actionPost()
    {

        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'get_poll':
                return $this->_get_poll();
            case 'get_video':
                return $this->_get_video();
            case 'checkUrl':
                return $this->_checkUrl();
        }

        return $this->_response->setStatus(\System\HttpResponse::S4_BAD_REQUEST);
    }


    protected function _get_poll()
    {
        $url = $this->_request->post['url']->string();

        $path = $this->parseUrl($url);

        if (!$path) {
            $this->_errors[] = 'Не удалось определить страницу';
        }

        $ownerType = $this->getOwnerType($path);

        if (!$ownerType) {
            return $this->_response->setJson([
                'success' => false,
                'error' => 1,
                'errorText' => 'Не удалось определить тип страницы',
            ]);
        }

        if (preg_match('@^(wall|post|topic)(.*)_(\d+)$@', $path, $matches)) {
            $vkType = $matches[1];
            $ownerId = $matches[2];
            $itemId = $matches[3];
        }

        if (!isset($ownerId) || !isset($itemId)) {
            return $this->_response->setJson(['success' => false, 'error' => 1, 'errorText' => 'Неправильная ссылка']);
        }

        $response = $this->VK->getPost($ownerId, $itemId, $ownerType, $this->check_access_token);

        $attachments = $response[0]['attachments'];
        $poll = null;

        foreach ($attachments as $attachment) {
            if ($attachment['type'] == 'poll') {
                $poll = $attachment;
            }
        }
        $answers = [];

        foreach ($poll['poll']['answers'] as $answer) {
            $answers[] = [
                'id' => $answer['id'],
                'title' => $answer['text'],
            ];
        }

        if ($poll == null) {
            return $this->_response->setJson([
                'success' => false,
                'error' => 1,
                'errorText' => 'По данной ссылке не найдено голосования',
            ]);
        }

        $poll['answers'] = $answers;
        $poll['poll']['anonymous'] = isset($poll['poll']['anonymous']) && $poll['poll']['anonymous'] ? true : false;

        return $this->_response->setJson(['success' => true, 'poll' => $poll]);
    }

    protected function _get_video()
    {
        $url = $this->_request->post['url']->string();

        $path = $this->parseUrl($url);

        if (!$path) {
            $this->_errors[] = 'Не удалось определить страницу';
        }

        if (preg_match('@^(video)(.*)_(\d+)$@', $path, $matches)) {
            $vkType = $matches[1];
            $ownerId = $matches[2];
            $itemId = $matches[3];
        }

        $ownerType = $this->getOwnerType($path);

        if (!isset($ownerId) || !isset($itemId) || !$ownerType) {
            return $this->_response->setJson([
                'success' => false,
                'error' => 1,
                'errorText' => 'Укажите ссылку на видео',
            ]);
        }

        $result = $this->VK->getVideo($ownerId, $itemId, $ownerType, $this->_application->User->access_token);

        if (isset($result['error'])) {
            return $this->_response->setJson([
                'success' => false,
                'json' => $result,
                'errorText' => 'Для проверки выполнения данного типа задания необходимо разрешить доступ приложению. <a target="_blank" href="https://oauth.vk.com/authorize?client_id=' . VK_ID . '&display=page&redirect_uri=' . VK_REDIRECT_URL . '&scope=offline,video,photo&response_type=code&state=close&v=5.64">Разрешить</a>',
            ]);
        } else {
            return $this->_response->setJson(['success' => true]);
        }
    }

    /*
     * ссылка на пост, фото или видео может быть с тире и без поэтому проверяем двумя регулярками
     */
    protected function pregMatchFor_Post_Photo_Video($path)
    {
        preg_match('@^(photo|wall|post|video)(\d+)_(\d+)@', $path, $matches);
        if (!$matches) preg_match('@^(photo|wall|post|video)-(\d+)_(\d+)@', $path, $matches);
        return $matches;
    }

    /*
     * получает тип владеьца (пользователь = 1 или группа = 2)
     */
    protected function getOwnerType($path)
    {
        if (preg_match('@^(photo|wall|post|video|topic)(\d+)_(\d+)@', $path, $matches)) {
            return 1;
        } else if (preg_match('@^(photo|wall|post|video|topic)-(\d+)_(\d+)@', $path, $matches)) {
            return 2;
        }
        return 0;
    }

    protected function pregMatchForComment($path)
    {
        preg_match('@^(photo|wall|video|topic)(.*)_(\d+)_r(\d+)$@', $path, $matches);
        return $matches;
    }

    protected function parseUrl($url)
    {

        $path = '';
        $url = trim($url);
        $data = parse_url($url);

        if (!empty($data['query'])) {

            parse_str($data['query'], $query);

            //для ссылок вида: https://vk.com/team?w=wall-22822305_1229241
            // https://vk.com/vk?z=photo-22822305_457313982
            if (!empty($query['z'])) {
                list($path) = explode('/', $query['z']);
            } else if (!empty($query['w'])) {
                $path = $query['w'];
            }
        }

        if (empty($path)) $path = trim($data['path'], '/');

        return $path;
    }

    /*
     * Валидация ссылок при оформлении задания
     * вызывается ajax и также в методе _edit()
     */
    protected function _checkUrl()
    {

        //подстказка
        //type - это наши типы заданий ['likes', 'reposts', 'join', 'friends', 'comments', 'views', 'polls', 'video'])
        //vkType = это типы страниц ВК (группа, страница, пост и тд)
        //vkTypePost - это подвыбор у заданий например лайки на пост, запись, фото


        $this->_limits = json_decode(file_get_contents(Model_Config::$limitsPath), true);

        $url = $this->_request->post['url']->string();
        $type = $this->_request->post['type']->string();
        $vkTypePost = $this->_request->post['vkType']->string();

        $age_limits = 0;
        $members_count = 0;
        $vkType = '';
        $itemId = '';
        $ownerId = '';

        if (!$url) return $this->_response->setJson(['success' => false, 'errorText' => 'Укажите УРЛ задания!']);

        $path = $this->parseUrl($url);

        if (!$path) return $this->_response->setJson(['success' => false, 'errorText' => 'Не удалось определить страницу!']);

        //если ссылка на пост, фото или видео
        if (in_array($type, ['likes', 'reposts', 'comments', 'polls', 'views', 'video']) and $vkTypePost != 'comment') {

            $matches = $this->pregMatchFor_Post_Photo_Video($path);
            $vkType = $matches[1]; //wall,photo,video
            if ($vkType == 'wall') $vkType = 'post';

            $ownerId = $matches[2];
            $itemId = $matches[3];
            $ownerType = $this->getOwnerType($path);

            if (!$ownerType) $errorText = 'Не удалось определить тип ссылки';

            if ($type == 'polls' and $vkType != 'post') $errorText = 'Указанная ссылка не является ссылкой на опрос!';
            else if ($type == 'views' and $vkType != 'post') $errorText = 'Для задания просмотры постов необходимо указать ссылку на пост';
            else if ($type == 'video' && $vkType != 'video') $errorText = 'Указанная ссылка не является ссылкой на видео!';
            if (isset($errorText)) return $this->_response->setJson(['success' => false, 'errorText' => $errorText,]);

            if ($vkType == 'photo') {
                $response = $this->VK->getPhotoById($ownerId, $itemId, $ownerType, $this->check_access_token);
            } else if ($vkType == 'video') {
                $response = $this->VK->getVideo($ownerId, $itemId, $ownerType, $this->check_access_token);
            } else {
                $response = $this->VK->getPost($ownerId, $itemId, $ownerType, $this->check_access_token);
            }

            if (!empty($response['error'])) $errorText = "Не удалось получить {$this->_vkTypes[$vkType]} по ссылке: {$url} Проверьте правлиьность ссылки!";
            if (isset($errorText)) return $this->_response->setJson(['success' => false, 'errorText' => $errorText,]);

        } else if (in_array($type, ['likes', 'reposts']) and $vkTypePost == 'comment') {
            //для лайков и репостов на комментарии

            $matches = $this->pregMatchForComment($path);

            if ($matches[1] == 'wall') $vkType = 'comment';
            else $vkType = $matches[1] . '_comment';

            $ownerId = $matches[2];
            $itemId = $matches[3];
            $commentId = $matches[4];
            $ownerType = $this->getOwnerType($path);

            if (!$ownerType) {
                return $this->_response->setJson([
                    'success' => false,
                    'errorText' => 'Не удалось определить тип ссылки',
                ]);
            }

            $response = $this->VK->getPostComments($ownerId, $itemId, $ownerType, $this->check_access_token, 1);

            if (!empty($response['error'])) {
                return $this->_response->setJson([
                    'success' => false,
                    'errorText' => 'Ссылка не подходит. Доступ к стене закрыт.',
                ]);
            }

        } else if ($type == 'friends') {

            //защита от дурака
            if (preg_match('@^public(\d+)$@', $path, $matches)) {
                return $this->_response->setJson(['success' => false, 'errorText' => 'Для заказа друзей укажите ссылку на профиль пользователя, а не на группу!']);
            }

            //ссылка на профиль может быть с id или с алиасом!
            if (preg_match('@^id(\d+)$@', $path, $matches)) {
                $user_id = $matches[1];
            } else {
                $user_id = $path;
            }

            $response = $this->VK->getUser($user_id, $this->check_access_token);

            if (empty($response[0]['id'])) $errorText = 'Не удалось получить пользователя по указанной ссылке!';
            //else if ($response[0]['is_closed']) $errorText = 'Этот профиль закрыт!'; Разрешаем создавать задания на закрытие профили! тк удается проверять выполнение таких заданий!
            else if ($response[0]['deactivated'] == 'banned') $errorText = 'Этот профиль заблокирован!';
            else if ($response[0]['deactivated'] == 'deleted') $errorText = 'Этот профиль удален!';
            else if (!empty($response[0]['id'])) {
                $ownerId = $response[0]['id'];
                $vkType = 'user';
                $ownerType = 1;
            }

            if (isset($errorText)) return $this->_response->setJson(['success' => false, 'errorText' => $errorText]);

            //это выключил! вроде можно сейчас создавать задания на закрытые профили?!
//            $response = $this->VK->getSubscriptions($ownerId, $this->check_access_token);
//            if (isset($response['error']) && $response['error'] > 0 && !isset($response['users'])) {
//                return $this->_response->setJson([
//                    'success' => false,
//                    'errorText' => 'Нельзя создавать задания на закрытый профиль. ',
//                ]);
//            }


        } else if ($type == 'join') {

            //защита от дурака
            if (preg_match('@^id(\d+)$@', $path, $matches)) {
                return $this->_response->setJson(['success' => false, 'errorText' => 'Для заказа подписчиков в группу укажите ссылку на группу, а не на профиль!']);
            }

            //ссылка на группу может быть с public или с алиасом!
            if (preg_match('@^public(\d+)$@', $path, $matches)) {
                $group_id = $matches[1];
            } else {
                $group_id = $path;
            }

            //получаем информацию о группе
            $result = $this->VK->getGroup($group_id, $this->check_access_token);

            if (empty($result[0]['id'])) $errorText = 'Не удалось получить группу по указанной ссылке!';
            else if ($result[0]['is_closed'] == 1) $errorText = 'Эта закрытая группа!';
            else if ($result[0]['is_closed'] == 2) $errorText = 'Эта частная группа!';
            else if ($result[0]['deactivated'] == 'banned') $errorText = 'Эта группа заблокирована!';
            else if ($result[0]['deactivated'] == 'deleted') $errorText = 'Эта группа удалена!';
            else if (!empty($result[0]['id'])) {
                $ownerId = $result[0]['id'];
                $age_limits = $result[0]['age_limits'];
                $members_count = $result[0]['members_count'];
                $vkType = 'group';
                $ownerType = 2;
            }

            if (isset($errorText)) return $this->_response->setJson(['success' => false, 'errorText' => $errorText]);
        }


        //здесь уже обязательно должен быть vkType!
        if (!$vkType) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => 'Не определен обязательный параметр vkType! Проверьте правильность ссылки!',
            ]);
        }


        //проверка для лайков, репостов и комментариев (проверить это условие!)
        if ($vkType != $vkTypePost and in_array($type, ['likes', 'reposts', 'comments'])) {
            if ($vkTypePost == 'comment' && strpos($vkType, 'comment') === false) {
                return $this->_response->setJson([
                    'success' => false,
                    'errorText' => 'Указанная ссылка не является ссылкой на ' . $this->_vkTypes[$vkTypePost],
                ]);
            } elseif ($vkTypePost != 'comment') {
                return $this->_response->setJson([
                    'success' => false,
                    'errorText' => 'Указанная ссылка не является ссылкой на ' . $this->_vkTypes[$vkTypePost],
                ]);
            }
        }

        if (!$ownerType) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => 'Не определен обязательный параметр ownerType! Обратитесь пожалуйста в поддержку!',
            ]);
        }

        if (!$ownerId) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => 'Не определен обязательный параметр ownerId! Обратитесь пожалуйста в поддержку!',
            ]);
        }

        if (!$itemId and in_array($type, ['likes', 'reposts', 'comments', 'polls', 'views', 'video'])) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => 'Не определен обязательный параметр itemId! Обратитесь пожалуйста в поддержку!',
            ]);
        }

        return $this->_response->setJson([
            'success' => true,
            'age_limits' => $age_limits,
            'members_count' => $members_count,
            'limits' => $this->_limits['groups'],
            'ownerType' => $ownerType,
            'vkType' => $vkType,
            'ownerId' => $ownerId,
            'itemId' => $itemId,
        ]);
    }


    /*
     * Добавление и редактирование задания
     */
    protected function _edit()
    {

        $type = $this->_request->post['type']->enum('',
            ['likes', 'reposts', 'join', 'friends', 'comments', 'views', 'polls', 'video']);

        $this->_application->User->makeShadow();
        $taskId = $this->_request->post['taskId']->int(0);

        //получаем: ownerType vkType ownerId itemId
        $checkUrl = json_decode($this->_checkUrl(), true);
        if (!$checkUrl['success']) {
            $this->_errors[] = $checkUrl['errorText'];
            return null;
        }
        $ownerType = $checkUrl['ownerType'];
        $vkType = $checkUrl['vkType'];
        $ownerId = $checkUrl['ownerId'];
        $itemId = $checkUrl['itemId'];


        //старый вариант - можно удалить?
        //если нет taskId значит создание нового задания
//        if (!$taskId) {
//            $response = $this->_task_check(); //проверяем на дубликат
//            $json = json_decode($response->getBody(), true);
//
//            if (!$json['success']) {
//                $this->_errors[] = 'Нельзя создать дубликат задания!';
//            }
//        }


        $prices = [
            'likes' => floatval($this->_application->settings['price_likes_buy']),
            'reposts' => floatval($this->_application->settings['price_reposts_buy']),
            'comments' => floatval($this->_application->settings['price_comments_buy']),
            'join' => floatval($this->_application->settings['price_join_buy']),
            'friends' => floatval($this->_application->settings['price_friends_buy']),
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
            'percent_city_my' => floatval($this->_application->settings['percent_city_my']),
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


        /*
         * РЕДАКТИРОВАНИЕ ЗАДАНИЯ
         */

        //Если передан $taskId значит редактирование задания!
        if ($taskId) {

            $task = $this->factoryTasks->tasks->getById($taskId, true);

            if ($task === null) {
                $this->_errors[] = 'Предыдущее задание не найдено';
            } else {
                if ($task->userId != $this->_application->UserID and $this->_application->UserID != \Config::$adminId) {
                    $this->_errors[] = 'Нет доступа к заданию!';
                }

                //лишнее условие?
//                elseif ($task->url != $this->_request->post['url']->string()) {
//                    $this->_errors[] = 'Предыдущее задание не найдено!';
//                }

                else {
                    if (!$task->isDel) {
                        //возвращаем пользователю на баланс за оставшиеся не выполненные работы по заданию
                        $sum = $task->price * $task->countRemain;
                        $this->_application->User->balance += $sum;
                    }

                    $this->_task = $task;
                }
            }
        }

        $this->_task->type = $type;
        $this->_task->minKarma = intval($this->_request->post['minKarma']->enum(0, [25, 50, 75]));
        $this->_task->followersOnly = $this->_request->post['followersOnly']->bool(false);
        $this->_task->newFollowers = $this->_request->post['newFollowers']->bool(false);
        $this->_task->prior = $this->_request->post['prior']->bool(false);
        $this->_task->count = $this->_request->post['count']->int(0);
        $this->_task->countReady = 0;
        $this->_task->countReadyBot = 0;
        $this->_task->countRemain = $this->_task->count - $this->_task->countReady;
        $this->_task->userId = $this->_application->UserID;
        $this->_task->url = $this->_request->post['url']->string();
        $this->_task->targeting = $this->_request->post['targeting']->bool();
        $this->_task->sex = $this->_request->post['sex']->int(0);
        $this->_task->ageFrom = $this->_request->post['ageFrom']->int(0);
        $this->_task->ageTo = $this->_request->post['ageTo']->int(0);
        $this->_task->cityId = 0;
        $this->_task->countryId = 0;
        $this->_task->pollId = $this->_request->post['pollId']->int(0);
        $this->_task->answerId = $this->_request->post['answerId']->int(0);
        $this->_task->answerIds = $this->_request->post['answerIds']->string('');
        $this->_task->isAnonymous = $this->_request->post['isAnonymous']->bool(false);
        $this->_task->active = true;
        $this->_task->isDel = false;
        $this->_task->reason = '';

        //данные полученные из _checkUrl()
        $this->_task->ownerType = (int)$ownerType;
        $this->_task->vkType = (string)$vkType;
        $this->_task->itemId = (string)$itemId;
        if ($ownerType == 1) $this->_task->ownerId = (string)$ownerId;
        else if ($ownerType == 2) $this->_task->ownerId = '-' . $ownerId;

        //для опросов (можно это вынести в _checkUrl() ???)
        if ($this->_task->type == 'polls') {
            $answers = $this->_request->post['answers']->asArray();

            $this->_task->answerTitle = \Lib_Html::HTMLOut($answers[$this->_task->answerId] ?: 'Любой вариант');

            if (!$this->_task->pollId) {
                $this->_errors[] = 'Не удалось найти голосование';
            }
        }

        //Для комментариев проверка (можно это вынести в _checkUrl() ???)
        $this->_task->commentType = $this->_request->post['commentType']->enum(0, [0, 1, 2, 3],
            \System\HttpRequest::INTEGER_NUM);
        $comments = $this->_request->post['comments']->asArray([], \System\HttpRequest::OUT_HTML_CLEAN);

        foreach ($comments as $id => $comment) {

            if (strlen($comment) == 0) {
                unset($comments[$id]);
            }

            if (strpos($comment, '@') !== false) {
                $this->_errors[] = 'В комментариях нельзя указывать ссылки';
                unset($comments[$id]);
            }

            if (strpos($comment, 'http') !== false) {
                $this->_errors[] = 'В комментариях нельзя указывать ссылки';
                unset($comments[$id]);
            }
        }

        if ($this->_task->commentType == 3 && count($comments) < $this->_task->count) {
            $this->_errors[] = 'Количество выполнений задания не должно превышать количество заданных комментариев';
        }

        $this->_task->setComments($comments);


        $cityId = $this->_request->post['city']->int(0);
        $this->_task->cityId = 0;
        $this->_task->countryId = 0;

        if ($cityId > 0) {
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

        if (!$this->_task->url) {
            $this->_errors[] = 'Укажите УРЛ задания';
            return null;
        }

        //приводим ссылку к нужному виду
        //$this->_task->url = strtr($this->_task->url, ['http://' => '', 'https://' => '', 'vk.com' => '']); //для чего это???
        $this->_task->url = trim($this->_task->url);
        $data = parse_url($this->_task->url);

        if (!isset($data['scheme'])) $data['scheme'] = 'https';
        if (!isset($data['host'])) $data['host'] = 'vk.com';

        $this->_task->url = $data['scheme'] . '://' . $data['host'] . '/' . $data['path'] . (isset($data['query']) ? ('?' . $data['query']) : '');

        if ($this->_task->vkType == 'user') {

            $result = $this->VK->getUser($this->_task->ownerId, $this->check_access_token);

            $this->_task->itemId = '';
            $this->_task->title = $result[0]['last_name'] . ' ' . $result[0]['first_name'];

            if ($result[0]['has_photo']) {
                $photo = [
                    'small' => $this->savePhoto($result[0]['photo_50']),
                    'big' => $this->savePhoto($result[0]['photo_200']),
                ];
                $this->_task->setPhoto($photo);
            }

        }

        if ($this->_task->vkType == 'wall') $this->_task->vkType = 'post';

        if (!$this->_task->type) $this->_errors[] = 'Укажите тип задания';


        //Сохраняем изображения
        switch ($this->_task->vkType) {
            case 'post':

                $result = $this->VK->getPost($this->_task->ownerId, $this->_task->itemId, $task->ownerType, $this->check_access_token);

                if (!empty($result['error'])) {
                    $this->_errors[] = 'Не удалось получить пост';
                }

                if ($this->_task->type == 'comments' && empty($result[0]['comments']['can_post'])) {
                    $this->_errors[] = 'Для данного поста комментарии запрещены';
                }


                foreach ($result[0]['attachments'] as $attachment) {

                    if ($attachment['type'] == 'photo') {

                        //Сохраним самое маленькое изображение (для миниатюры задания)
                        $photoUrl = $attachment['photo']['sizes'][0]['url'];

                        $photo = [
                            'small' => $this->savePhoto($photoUrl),
                        ];
                        $this->_task->setPhoto($photo);
                        break;
                    }
                }

                $this->_task->title = \Lib_Text::Truncate(strip_tags($result[0]['text']));

                break;
            case 'photo':

                $result = $this->VK->getPhotoById($this->_task->ownerId, $this->_task->itemId, $this->_task->ownerType, $this->check_access_token);

                if (!isset($result[0])) {
                    $this->_errors[] = 'Не удалось найти фото';
                }

                if ($this->_task->type == 'comments' && !$result[0]['can_comment']) {
                    $this->_errors[] = 'Для данного фото комментарии запрещены';
                }

                $photo = [
                    'small' => $this->savePhoto($result[0]['photo_75']),
                ];
                $this->_task->setPhoto($photo);
                $this->_task->title = 'Фотография';
                break;
            case 'video':

                $result = $this->VK->getVideo($this->_task->ownerId, $this->_task->itemId, $this->_task->ownerType, $this->check_access_token);

                if (isset($result['items'][0])) {
                    $this->_task->title = $result['items'][0]['title'];
                    $photo = [
                        'small' => $this->savePhoto($result['items'][0]['image'][0]['url']),
                    ];
                    $this->_task->setPhoto($photo);
                }

                if ($this->_task->type == 'comments' && !$result['items'][0]['can_comment']) {
                    $this->_errors[] = 'Для данного видео комментарии запрещены';
                }
                $this->_task->title = 'Видео';
                break;
            case 'comment':
                $offset = 0;
                do {

                    $response = $this->VK->getPostComments($task->ownerId, $task->itemId, $task->ownerType, $this->check_access_token, 100, $offset);

                    if ($response['error'] > 0) {
                        $this->_errors[] = 'Для данного типа задания необходим открытый доступ к стене';
                    }
                    $uid = 0;

                    foreach ($response['items'] as $item) {
                        if ($item['id'] == $this->_task->commentId) {
                            $this->_task->title = $item['text'];
                            $uid = $item['from_id'];
                            break;
                        }
                    }
                    $found = false;

                    if ($uid > 0) {
                        foreach ($response['profiles'] as $profile) {
                            if ($profile['id'] == $uid) {
                                $photo = [
                                    'small' => $this->savePhoto($profile['photo_50']),
                                ];
                                $this->_task->setPhoto($photo);
                                $found = true;
                            }
                        }
                    }

                    if ($found) {
                        break;
                    }
                    $offset += 100;
                    usleep(100000);
                } while ($offset < $response['count']);
                break;
        }

        $photo = $this->_task->getPhoto();

        //здесь не удается получить фото (это все нужно проверить!)
        if (!isset($photo['small'])) {

            if (intval($this->_task->ownerId) > 0) {

                $result = $this->VK->getUser($this->_task->ownerId, $this->check_access_token);

                $photo = [
                    'small' => $this->savePhoto($result[0]['photo_50']),
                ];

                $this->_task->setPhoto($photo);
            } else {

                $result = $this->VK->getGroup(abs($this->_task->ownerId), $this->check_access_token);

                $this->_task->age_limits = intval($result[0]['age_limits']);

                if (isset($result[0]['photo_100']) && $result[0]['photo_100'] != '') {
                    $photo = [
                        'small' => $this->savePhoto($result[0]['photo_100']),
                    ];
                    $this->_task->setPhoto($photo);
                }
            }
        }

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

        if ($this->_task->cityId > 0) {
            if ($this->_task->cityId == $this->_application->User->cityId) {
                $sum += $price * $percents['percent_city_my'] / 100;
            } else {
                $sum += $price * $percents['percent_city'] / 100;
            }
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

        if (isset($this->_errors) and is_array($this->_errors) and count($this->_errors)) {
            return null;
        }
        if($this->_task->count) $this->_task->price = $sum / $this->_task->count;
        $this->_task->sum = floatval($sum);

        if (!$this->_task->age_limits && intval($this->_task->ownerId) < 0) {
            usleep(100000);

            $result = $this->VK->getGroup(abs($this->_task->ownerId), $this->check_access_token);

            $this->_task->age_limits = intval($result[0]['age_limits']);
        }

        if ($this->_task->sex || $this->_task->ageFrom || $this->_task->ageTo || $this->_task->cityId ||
            $this->_task->relation || $this->_task->avatarCount || $this->_task->filled || $this->_task->pageAge ||
            $this->_task->followersCount || $this->_task->interestingPage || $this->_task->frequencyPost
        ) {
            $this->_task->targeting = true;
        } else {
            $this->_task->targeting = false;
        }

        //списываем деньги за задание и делаем запись в историю баланса
        if ($this->factoryTasks->tasks->save($this->_task)) {

            $this->_application->User->makeShadow();

            $balance = $this->factoryUsers->users->balance->getNew();
            $balance->userId = $this->_application->User->userId;
            $balance->isTask = true;
            $balance->balance = -$sum;
            $balance->balanceFrom = $this->_application->User->balance;
            $this->_application->User->balance -= $sum;
            $balance->balanceTo = $this->_application->User->balance;
            $balance->dateCreate = time();

            if ($taskId) $balance->comment = 'Обновление задания';
            else $balance->comment = 'Создание задания';

            if ($this->factoryUsers->users->save($this->_application->User)) {
                $this->factoryUsers->users->balance->save($balance);
            }

            $query = $this->factoryTasks->tasks->query();
            $query->filter
                ->fieldValue('type', '=', $this->_task->type)
                ->fieldValue('vkType', '=', $this->_task->vkType)
                ->fieldValue('taskId', '!=', $this->_task->taskId)
                ->fieldValue('ownerId', '=', $this->_task->ownerId)
                ->fieldValue('itemId', '=', $this->_task->itemId);
            $it = $query->iterator();

            /** @var Model_Tasks_Task $task */
            foreach ($it as $task) {

                $users = $this->factoryTasks->users->getByTaskId($task->taskId);

                foreach ($users as $user) {

                    //это что????
                    if ($user->isDone) {

                        $taskUser = $this->factoryTasks->users->getByTaskIdUserId($this->_task->taskId, $user->userId,
                            true);

                        if ($taskUser === null) {
                            $taskUser = $this->factoryTasks->users->getNew();
                        }

                        $taskUser->taskId = $this->_task->taskId;
                        $taskUser->userId = $user->userId;
                        $taskUser->uid = $user->uid;
                        $taskUser->type = $this->_task->type;
                        $taskUser->isDel = true;
                        $taskUser->isDelDate = time();
                        $taskUser->isDone = false;
                        $taskUser->isActive = false;

                        $this->factoryTasks->users->save($taskUser);
                    }
                }
            }


            //создаем сообщение о созданном задании
            $mConfig = \Service\Messages\Model_Config::GetConfig();

            $message = $this->factoryMessages->users->getNew();
            $message->userId = $this->_task->userId;
            $message->isDone = false;
            $message->type = \Service\Messages\Model_Config::TYPE_SYSTEM;

            $text = $mConfig['tasks']['types']['add']['text'];
            $text = str_replace('%title%', $this->_task->title, $text);
            $message->text = $text;

            $message->icon = 'tasks';
            $this->factoryMessages->users->save($message);

            if ($taskId) return $this->_response->setLocation('/tasks/my/all/1?taskId=' . $taskId);
            else return $this->_response->setLocation('/tasks/my/all/1');
        }

        return $this->_response->setStatus(\System\HttpResponse::S4_BAD_REQUEST);
    }

    /*
     * Проверка на дубль задания
     */
    protected function _double_task_check()
    {

        $response = clone $this->_response;
        $url = $this->_request->post['url']->string();
        $type = $this->_request->post['type']->enum('',
            ['likes', 'reposts', 'join', 'friends', 'comments', 'views', 'polls', 'video']);

        $path = $this->parseUrl($url);

        if (in_array($type, ['likes', 'reposts', 'comments', 'polls', 'views', 'video'])) {

            //ссылка на пост
            $matches = $this->pregMatchFor_Post_Photo_Video($path);
            $ownerId = $matches[2];
            $itemId = $matches[3];

            //ищем задание в базе данных
            if(!empty($ownerId) and !empty($itemId))
            $task = $this->factoryTasks->tasks->findTask([
                'userId' => $this->_application->UserID, //раскомментировать просле проверки
                'type' => $type,
                'ownerId' => $ownerId,
                'itemId' => $itemId,
            ]);


        } else if ($type == 'join') {

            if (preg_match('@^public(\d+)$@', $path, $matches)) $group_id = $matches[1];
            else $group_id = $path;

            $result = $this->VK->getGroup($group_id, $this->check_access_token);

            //здесь нужно выбросить ошибку если не удалось получить группу!
            if (!isset($result[0]['id'])) return $response->setJson(['success' => false]);

            //ищем задание в базе данных
            $task = $this->factoryTasks->tasks->findTask([
                'userId' => $this->_application->UserID, //раскомментировать просле проверки
                'type' => $type,
                'ownerId' => $result[0]['id'],
            ]);

        } else if ($type == 'friends') {

            //получаем пользователя
            $response = $this->VK->getUser($path, $this->check_access_token);

            if (isset($result[0]['id'])) $ownerId = $result[0]['id'];

            //ищем задание в базе данных
            $task = $this->factoryTasks->tasks->findTask([
                'userId' => $this->_application->UserID,
                'type' => $type,
                'ownerId' => $ownerId,
            ]);

        }



        //если получилось получить id группы то ищем задание по id группы (ownerId)
        //if (isset($result[0]['id'])) $query->filter->fieldValue('ownerId', '=', "-{$result[0]['id']}");
        //иначе ищем по url (менее надежно тк можно создавать дубли заданий на одну и туже группу с разными url)
        //else $query->filter->fieldValue('url', '=', $url);


        if (!empty($task)) {
            $vars = ['taskId' => $task->taskId];
            $html = \STPL::Fetch('client/edit/check', $vars);
            return $this->_response->setJson(['success' => false, 'html' => $html]);
        }

        return $response->setJson(['success' => true]);
    }

    protected function savePhoto($url)
    {

        //$temp = explode('?', $url);
        //$arr = explode('.', $temp[0]);
        //$ext = array_pop($arr);
        $ext = 'jpeg';

        $data = file_get_contents($url);

        $image = new \Imagick();
        $image->readImageBlob($data);

        $path = 'tasks/' . date('Y') . '_' . date('m') . '/' . rand(10, 99) . '/' . rand(10, 99) . '/' . rand(10, 99) . '/';
        $file = mb_substr(md5(\Lib_Uuid::getNext()), 0, 11) . '.' . $ext;

        if (!is_dir(IMAGES_PATH . $path)) {
            mkdir(IMAGES_PATH . $path, 0777, true);
        }

        $res = $image->writeImage(IMAGES_PATH . $path . $file);

        $photo = [
            'path' => $path,
            'file' => $file,
            'url' => '/img/' . $path . $file,
            'w' => $image->getImageWidth(),
            'h' => $image->getImageHeight(),
        ];

        return $photo;
    }

    /**
     * @param Model_Tasks_Task $task
     * @param string $reason
     */
    protected function taskStop(Model_Tasks_Task $task, string $reason)
    {
        $task->makeShadow();
        $task->active = false;
        $task->reason = $reason;
        $this->factoryTasks->tasks->save($task);
    }

}
