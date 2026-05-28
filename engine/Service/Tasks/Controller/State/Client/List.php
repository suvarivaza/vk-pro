<?php

//INFO: Контроллер листа заданий
//INFO: здесь происходит проверка выполнения задания пользователем

namespace Service\Tasks;

use System\HttpResponse;

class Controller_State_Client_List extends Controller_State_Client
{
    protected $_vkTypes = [
        'post' => 'Запись на стене',
        'photo' => 'Фотография',
        'video' => 'Видеозапись',
        'comment' => 'Комментарий',
    ];
    protected $karmaParams = null;
    protected $_fields = [
        'comments' => [
            'limit' => true,
            'comment' => 'Оставил комментарий',
        ],
        'join' => [
            'limit' => true,
            'comment' => 'Подписался на группу',
        ],
        'friends' => [
            'limit' => true,
            'comment' => 'Добавиться в друзья',
        ],
        'likes' => [
            'limit' => true,
            'comment' => 'Поставил лайк',
        ],
        'polls' => [
            'limit' => true,
            'comment' => 'Участие в опросе',
        ],
        'reposts' => [
            'limit' => true,
            'comment' => 'Репост',
        ],
        'video' => [
            'limit' => false,
            'comment' => 'Просмотр видео',
        ],
        'views' => [
            'limit' => false,
            'comment' => 'Просмотр поста',
        ],
    ];
    private $userLimits = null;


    /*
     * Выполняется первым. Проверяем авторизацию, подключаем скрипты и тд
     */
    public function actionPrepare()
    {

        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setLocation('/users/login');
        }

        //var_dump($this->_application->User->bad);

        $this->karmaParams = json_decode(file_get_contents(\Service\Users\Model_Config::$karmaPath), true);

        $this->_application->page = 'tasks-work';

        $this->_application->Title->addScripts(['/js/server.js?' . filemtime(VAR_PATH . 'js/server.js')]);

        if (isset($this->_request->get['idDel'])) {

            $task = $this->factoryTasks->tasks->getById($this->_request->get['idDel']->int(0), true);

            if ($task !== null) {
                $taskUser = $this->factoryTasks->users->getByTaskIdUserId($task->taskId,
                    $this->_application->User->userId, true);

                if ($taskUser !== null) {
                    $taskUser->isDel = true;
                    $taskUser->isDelDate = time();
                    $this->factoryTasks->users->save($taskUser);
                }
            }

            return $this->_response->setLocation($this->_request->server['REDIRECT_URL']->string());
        }

        $this->userLimits = json_decode(file_get_contents(Model_Config::$limitsPath), true);

        $this->_application->Title->add('link', [
            'rel' => 'icon',
            'href' => '/img/icons/32/icon-tasks.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->add('link', [
            'rel' => 'shortcut icon',
            'href' => '/img/icons/32/icon-tasks.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->Title = 'Все задания';

        return parent::actionPrepare();
    }

    //Подготваливаем данные и вызываем шаблон страницы
    public function actionGet()
    {

        //Если пользователь заблокирован
        if ($this->_application->User->ban) {
            $vars = [
                'user' => $this->_application->User,
                'errors' => $this->_errors,
            ];
            return $this->_response->setBody(\STPL::Fetch('client/ban', $vars));
        }


        $taskId = $this->_request->get['taskId']->int(0);
        $page = $this->_request->get['p']->int(1);
        $url = "/tasks/{$this->_params['type']}?p=@p@";
        $limit = 10;
        $offset = ($page - 1) * $limit;

        //получаем бота пользователя
        $bot = $this->factoryBot->bots->getByUserId($this->_application->UserID);
        if ($bot !== null) $this->_application->User->isBot = $bot->isBot;

        //собираем массив типов заданий которые не выводим пользователю с включенным ботом
        $botTypes = [];
        if ($bot !== null && $bot->isActive) {
            foreach (\Service\Bot\Model_Config::$botTypes as $botTypeId => $botType) {
                if ($bot->isBot & $botTypeId) {
                    if ($botType !== 'comments' && $botType !== 'polls') {
                        $botTypes[] = $botType;
                    }
                }
            }
        }


        //получаем работы выполненных пользователем в статусе isActive = true и меняем на isActive = false
        //для чего это делаем???
        $query = $this->factoryTasks->users->query();
        $query->filter->fieldValue('userId', '=', $this->_application->UserID)
            ->fieldValue('isActive', '=', true);
        $tasksUser = $query->iteratorForSave();
        foreach ($tasksUser as $taskUser) {
            $taskUser->isActive = false;
            $this->factoryTasks->users->save($taskUser);
        }

        if ($this->_params['type'] === 'all' and $taskId) {

            $type = $this->_params['type'];
            $list = $this->factoryTasks->tasks->getItemsList($this->_application->User, $type, 1, 0, $taskId);

            //pd($list);

            //$list = [];
//            foreach ($this->_types as $type => $title) {
//
//                //не выводим задания для пользователей с отрицательным балансом или с плохими аккаунтами (кроме 'views', 'video', 'polls')
//                if (($this->_application->User->balance < 0 || $this->_application->User->bad > 0) && !in_array($type, ['views', 'video', 'polls'])) continue;
//
//                //Если бот активен то не выводим задания определенного типа
//                if (in_array($type, $botTypes)) continue;
//
//                if ($taskId == 0) {
//                    $listSpecialsTasks = $this->factoryTasks->tasks->getListSpecials($this->_application->User, $type, $limit, $offset);
//                    $list = array_merge($list, $listSpecialsTasks);
//                }
//
//                if ($limit > 0) {
//                    $tasksList = $this->factoryTasks->tasks->getItemsList($this->_application->User, $type, $limit, $offset, $taskId);
//                    $list = array_merge($list, $tasksList);
//                }
//            }


        } else {

            $type = $this->_params['type'];
            $list = [];

            if ($this->_application->User->balance < 0 && !in_array($type, ['views', 'video', 'polls'])) {
                $this->_errors[] = 'Пока ваш баланс имеет отрицательное значение, вам не доступно выполнение этого типа заданий. Что бы вывести баланс в положительное значение и получить доступ ко всем заданиям, выполняйте задания, которые невозможно отменить, или приобретите пакет баллов перекрывающий ваш минус.';
            } elseif ($this->_application->User->bad > 0 && !in_array($type, ['views', 'video', 'polls'])) {
                $this->_errors[] = 'Ваша страничка не проходит проверку на качество, вам не доступно выполнение этого типа заданий.';
            } elseif (in_array($type, $botTypes)) {
                $this->_errors[] = 'Данный тип заданий выполняет бот. Для предотвращения блокировки со стороны ВКонтакте такие задания нельзя выполнять вручную';
            } else {

                //получаем специальные задания
                $listSpecialsTasks = $this->factoryTasks->tasks->getListSpecials($this->_application->User, $type, $limit, $offset);

                //подсчитаем общее количество заданий для пагинации
                $totalSpecialsTasks = count($this->factoryTasks->tasks->getListSpecials($this->_application->User, $type, null, null));
                $totalTasks = count($this->factoryTasks->tasks->getItemsList($this->_application->User, $type, null, null));
                $total = $totalSpecialsTasks + $totalTasks;

                if ($listSpecialsTasks) {
                    //пока есть специальные задания то выводим их первыми и дополняем остальными заданиями
                    $newLimit = $limit - count($listSpecialsTasks);
                    $tasksList = $this->factoryTasks->tasks->getItemsList($this->_application->User, $type, $newLimit, 0);
                } else {
                    //если специальных заданий уже нет то выводим обычные задания
                    $offset -= $totalSpecialsTasks;
                    $tasksList = $this->factoryTasks->tasks->getItemsList($this->_application->User, $type, $limit, $offset);
                }


                $list = array_merge($listSpecialsTasks, $tasksList);

            }

        }

        usort($list, [$this, '_sort_list']);
        //$list = array_slice($list, $offset, $limit);

        //тут что то делаем с зааданиями
        foreach ($list as $task) {
            $taskUser = $this->factoryTasks->users->getByTaskIdUserId($task->taskId, $this->_application->UserID, true);

            if ($taskUser === null) {
                $taskUser = $this->factoryTasks->users->getNew();
                $taskUser->taskId = $task->taskId;
                $taskUser->type = $task->type;
                $taskUser->userId = $this->_application->UserID;
                $taskUser->uid = $this->_application->User->uid;
                $taskUser->isDel = false;
                $taskUser->isDone = false;
                $taskUser->isActive = true;
                $this->factoryTasks->users->save($taskUser);
            } else {
                $taskUser->isActive = true;
                $this->factoryTasks->users->save($taskUser);
            }
        }

        $bonus = null;

        if ($this->_application->User->bonus) {
            $bonus = \Service\Users\Model_Config::GetBonusSettings();
        }


        if ($type == 'friends' || $type == 'polls') {
            $badTokenErrorText = $this->isBadUserAccessToken();
        }

        $pageslink = \Lib_Html::GetNavigationPagesNumber(
            $limit,
            10,
            $total,
            $page,
            $url,
            1
        );


        $vars = [
            'karmaParams' => $this->karmaParams,
            'bonus' => $bonus,
            'errors' => $this->_errors,
            'user' => $this->_application->User,
            'prices' => $this->_application->settings,
            'type' => $this->_params['type'],
            'titles' => $this->_titles,
            'list' => $list,
            'types' => $this->_types,
            'pageslink' => $pageslink,
            'badTokenErrorText' => $badTokenErrorText ?? $badTokenErrorText
        ];



        return $this->_response->setBody(\STPL::Fetch('client/list', $vars));
    }

    public function actionPost()
    {

        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'taskCheck':
                return $this->_taskCheck();
            case 'taskPrepare':
                return $this->_taskPrepare();
            case 'abuse':
                return $this->_abuse();
            case 'unban':
                return $this->_unban();
            case 'botForm':
                return $this->_botForm();
            case 'isBotActive':
                return $this->_isBotActive();
        }

        return parent::actionPost();
    }

    /*
     * Проверка выполнения заданий выполняемых пользователями в листе заданий: https://vk-pro.top/tasks/
     */
    private function _taskCheck()
    {

        $task = $this->factoryTasks->tasks->getById($this->_request->post['taskId']->int());

        if ($task === null) return $this->_response->setJson(['success' => false]);

        $this->VK->setTask($task); //передадим задание в ВК для логов

        //проверяем не выполнял ли уже пользователь данное задание раньше (нужно разобраться почему так получается)
        //получаем задания выполненные пользователем
        $taskUser = $this->factoryTasks->users->getByTaskIdUserId($task->taskId, $this->_application->UserID, true);

        //Если задание уже было выполнено пользователем ранее!
        if ($taskUser !== null && $taskUser->isDone) {
            return $this->_response->setJson([
                'success' => true,
                'message' => 'Задание уже было выполнено Вами ранее',
                'karma' => $this->_application->User->karma,
                'karmaText' => number_format($this->_application->User->karma, 1),
                'balance' => number_format($this->_application->User->balance, 1, '.', ' ')
            ]);
        }

        //здесь так же можно сделать проверку на выполнение разных заданий но на одну группу или пост (по айди группы/поста)

        if (($response = $this->_checkLimits($task)) !== null) {
            return $response;
        }

        $type = $task->type;
        $response = null;



        //Вызываем нужный метод проверки в зависимости от типа задания
        switch ($type) {
            case 'likes':
                $response = $this->_checkLikes($task);
                break;
            case 'reposts':
                $response = $this->_checkReposts($task);
                break;
            case 'join':
                $response = $this->_checkGroup($task);
                break;
            case 'friends':
                $response = $this->_checkUser($task);
                break;
            case 'comments':
                $response = $this->_checkComments($task);
                break;
            case 'polls':
                $response = $this->_checkPolls($task);
                break;
            case 'views':
                $response = $this->_checkViews($task);
                break;
            case 'video':
                $response = $this->_checkVideo($task);
                break;
        }


        //Формируем ответ
        if ($response !== null) {

            $json = json_decode($response->getBody(), true);

            //если задание выполнено успено начисляем бонус
            if ($json['success']) {

                //Проверка для бонусных заданий
                //Проверяем является ли текущий объект экземпляром класса Controller_State_Client_List_Bonus
                if ($this instanceof Controller_State_Client_List_Bonus) {
                    $json['bonus'] = true;
                } else {
                    $json['bonus'] = false;
                    $json['karma'] = abs($this->_application->User->karma) > 100 ? 100 : abs($this->_application->User->karma);
                    $json['karmaText'] = number_format($this->_application->User->karma, 1);
                    $json['html'] = $this->_getTaskHTML();
                }

                $response->setJson($json);
            } else if (!$json['success'] and $json['removeTask']) {
                $json['html'] = $this->_getTaskHTML();
                $response->setJson($json);
            }

            return $response;
        }

        return $this->_response->setStatus(\System\HttpResponse::S4_BAD_REQUEST);
    }

    private function _checkLimits(Model_Tasks_Task $task)
    {


        if (isset($_SESSION[$task->type]['countDay']) && $_SESSION[$task->type]['countDay'] === true) {
            $limits = json_decode(file_get_contents(Model_Config::$limitsPath), true);
            $_SESSION[$task->type]['countDay'] = false;

            return $this->_response->setJson([
                'success' => false,
                'errorText' => $limits['user'][$task->type]['comment'],
            ]);
        }

        if (isset($_SESSION[$task->type]['countHour']) && $_SESSION[$task->type]['countHour'] === true) {
            $limits = json_decode(file_get_contents(Model_Config::$limitsPath), true);
            $_SESSION[$task->type]['countHour'] = false;

            return $this->_response->setJson([
                'success' => false,
                'errorText' => $limits['user'][$task->type]['comment_interval'],
            ]);
        }

        if (isset($_SESSION[$task->type]['count10Min']) && $_SESSION[$task->type]['count10Min'] === true) {
            $limits = json_decode(file_get_contents(Model_Config::$limitsPath), true);
            $_SESSION[$task->type]['count10Min'] = false;

            return $this->_response->setJson([
                'success' => false,
                'errorText' => $limits['user'][$task->type]['comment_interval'],
            ]);
        }

        if (isset($_SESSION[$task->taskId]['not_active']) && $_SESSION[$task->taskId]['not_active'] === true) {
            $_SESSION[$task->taskId]['not_active'] = false;

            return $this->_response->setJson([
                'success' => false,
                'errorText' => 'Невозможно выполнить задание. Задание не активно.',
                'close' => true,
            ]);
        }

        return null;
    }

    /*
     * проверка доступности поста
     * и обработка ошибок
     */
    protected function checkPost(\Service\Tasks\Model_Tasks_Task $task)
    {

        //return false;

        //пробуем получить пост
        $response = $this->VK->getPost($task->ownerId, $task->itemId, $task->ownerType, $this->check_access_token);


        //dd($response);

        if ($response === []) {
            $errorText = 'Невозможно выполнить задание! Объект не найден!';
        } //обработаем ошибки
        else if (!empty($response['error'])) {
            //здесь нужно обработать другие ошибки..
            $errorText = "Ошибка {$response['error']}: {$response['errorText']}";
        }

        //для _checkComments()
        if ($task->type == 'comments' and $response[0]['comments']['can_post'] == 0) {
            $errorText = 'Невозможно оставить комментарий! Пост не найден!';
        }

        if (!empty($errorText)) return $errorText;

        return false;
    }

    /*
     * проверка задания - поставить лайк
     */
    protected function _checkLikes(\Service\Tasks\Model_Tasks_Task $task)
    {

        //дальше будем использовать токен пользователя поэтому сначала проверим его
        if ($errorText = $this->isBadUserAccessToken()) {
            return $this->_response->setJson(['success' => false, 'errorText' => $errorText]);
        }

        //сначала проверим доступность поста
//        if ($errorText = $this->checkPost($task)) {
//            $this->taskStop($task, $errorText);
//            return $this->_response->setJson(['success' => false, 'errorText' => $errorText, 'removeTask' => true]);
//        }


        //теперь проеряем наличие лайка
        $uid = $this->_application->User->uid;
        $itemId = ($task->vkType == 'comment') ? $task->commentId : $task->itemId;
//        $response = $this->VK->isLiked($uid, $task->ownerId, $itemId, $task->vkType, $task->ownerType, $this->check_access_token);
        $response = $this->VK->isLiked($uid, $task->ownerId, $itemId, $task->vkType, $task->ownerType, $this->user_access_token);

//        return $this->_response->setJson(['success' => false, 'errorText' => json_encode($response)]);

        if (!empty($response['error'])) {
            return $this->_response->setJson(['success' => false, 'errorText' => "Ошибка {$response['error']} {$response['errorText']}"]);
        }


        if (!empty($response['liked'])) {

            //Задание успешно выполнено
            $this->taskSuccess($task);

            //Возвращаем успешный ответ
            return $this->_response->setJson([
                'success' => true,
                'message' => 'Лайк поста выполнен!',
                'karma' => $this->_application->User->karma,
                'karmaText' => number_format($this->_application->User->karma, 1),
                'balance' => number_format($this->_application->User->balance, 1, '.', ' '),
            ]);
        }


        return $this->_response->setJson(['success' => false, 'errorText' => 'Лайк не найден!']);
    }


    protected function _checkReposts(\Service\Tasks\Model_Tasks_Task $task)
    {

        //дальше будем использовать токен пользователя поэтому сначала проверим его
        if ($errorText = $this->isBadUserAccessToken()) {
            return $this->_response->setJson(['success' => false, 'errorText' => $errorText]);
        }

        //сначала проверим доступность поста
//        if ($errorText = $this->checkPost($task)) {
//            $this->taskStop($task, $errorText);
//            return $this->_response->setJson(['success' => false, 'errorText' => $errorText, 'removeTask' => true]);
//        }

        //теперь проверим репост (будет в списке мне нравится указанного пользователя)
        $response = $this->VK->isLiked($this->_application->User->uid, $task->ownerId, $task->itemId, $task->vkType, $task->ownerType, $this->user_access_token);

        //Здесь нужно еще реализовать проверку репоста комментария! На данный момент это не работает!

        //Репост найден
        if (!empty($response['copied'])) {

            //Задание выполнено
            $this->taskSuccess($task);

            //Возвращаем успешный ответ
            return $this->_response->setJson([
                'success' => true,
                'message' => 'Репост выполнен!',
                'karma' => $this->_application->User->karma,
                'karmaText' => number_format($this->_application->User->karma, 1),
                'balance' => number_format($this->_application->User->balance, 1, '.', ' '),
            ]);
        }

        //Возвращаем не успешный ответ
        return $this->_response->setJson(['success' => false, 'errorText' => 'Репост не найден']);
    }

    /*
     * Проверка задания - подписаться на группу
     */
    protected function _checkGroup(\Service\Tasks\Model_Tasks_Task $task)
    {

        //дальше будем использовать токен пользователя поэтому сначала проверим его
        if ($errorText = $this->isBadUserAccessToken()) {
            return $this->_response->setJson(['success' => false, 'errorText' => $errorText]);
        }

        $response = $this->VK->isMember($task->ownerId, $this->_application->User->uid, $this->user_access_token);

        if (isset($response['error']) and $response['error'] == 203 and $response['errorText'] == 'Access to group denied: access to the group members is denied') {
            $this->taskStop($task, 'Нет доступа к участникам группы!');
            return $this->_response->setJson(['success' => false, 'errorText' => "Извините, задание не доступно. Скрыты подписчики группы!"]);
        }


        //Если группа открытая и пользователь является участником группы то в member вернется 1
        //Если была подана заявка в закрытую группу то вернется request = 1
        //публичные страницы (page) тоже так проверяются
        //насчет мероприятий (event) пока не уверен
        if ((isset($response['member']) and $response['member'] == 1) or (isset($response['request']) and $response['request'] == 1)) {

            //Задание выполнено
            $this->taskSuccess($task);

            return $this->_response->setJson([
                'success' => true,
                'message' => 'Подписка выполнена!',
                'karma' => $this->_application->User->karma,
                'karmaText' => number_format($this->_application->User->karma, 1),
                'balance' => number_format($this->_application->User->balance, 1, '.', ' '),
            ]);
        }

        //если не удлалось проверить выше то возможно что то не так с группой
        //получим информацию о группе
        $response = $this->VK->getGroup($task->ownerId, $this->user_access_token);

        if (!empty($response['error'])) {
            return $this->_response->setJson(['success' => false, 'errorText' => "Ошибка {$response['error']} {$response['errorText']}"]);
        } else if (!empty($response[0]['is_closed']) and $response[0]['is_closed'] == 2) {
            $reason = 'Задание недоступно! Это частная группа!';
        } else if ($response[0]['deactivated'] == 'banned') {
            $reason = 'Задание недоступно! Группа забанена!';
        } else if ($response[0]['deactivated'] == 'deleted') {
            $reason = 'Задание недоступно! Группа удалена!';
        } else if ($response[0]['type'] == 'event') {
            $reason = 'Задание недоступно! Это мероприятие!';
        }

        if (!empty($reason)) {
            $this->taskStop($task, $reason);
            return $this->_response->setJson(['success' => false, 'errorText' => $reason, 'removeTask' => true]);
        }


        return $this->_response->setJson(['success' => false, 'errorText' => 'Вы не найдены в подписчиках группы!']);
    }


    /*
     * Проверка задания - добавить в друзья
     * для этого метода используется токен пользователя!
     */
    protected function _checkUser(\Service\Tasks\Model_Tasks_Task $task)
    {

        //пробуем получить пользователя
        $response = $this->VK->getUser($task->ownerId, $this->check_access_token);

        if (!empty($response['error'])) $errorText = "Ошибка {$response['error']} {$response['errorText']}";

        if (empty($response[0]['id'])) $errorText = 'Не удалось получить пользователя!';
        //elseif ($response[0]['is_closed']) $errorText = 'Этот профиль закрыт!'; //вроде успешно получается проверять закрытые профили!
        elseif ($response[0]['deactivated'] == 'banned') $errorText = 'Этот профиль заблокирован!';
        elseif ($response[0]['deactivated'] == 'deleted') $errorText = 'Этот профиль удален!';

        if (isset($errorText)) {
            $this->taskStop($task, 'Невозможно добавить в друзья! ' . $errorText);
            return $this->_response->setJson(['success' => false, 'errorText' => $errorText, 'removeTask' => true]);
        }

        //дальше будем использовать токен пользователя поэтому сначала проверим его
        if ($errorText = $this->isBadUserAccessToken()) {
            return $this->_response->setJson(['success' => false, 'errorText' => $errorText]);
        }


        //Проверяем есть ли текущий пользователь с токеном user_access_token в друзьях у пользователя $task->ownerId
        $response = $this->VK->areFriends($task->ownerId, $this->user_access_token);

        //Пользователь найден в друзьях
        if ($response[0]['friend_status']) {

            //Задание выполнено
            $this->taskSuccess($task);

            //Возвращаем успешный ответ
            return $this->_response->setJson([
                'success' => true,
                'message' => 'Запрос в друзья выполнен!',
                'karma' => $this->_application->User->karma,
                'karmaText' => number_format($this->_application->User->karma, 1),
                'balance' => number_format($this->_application->User->balance, 1, '.', ' '),
            ]);
        }

        //Возвращаем не успешный ответ
        return $this->_response->setJson(['success' => false, 'errorText' => 'Заявка в друзья не найдена!']);
    }


    /*
     * Проверка задания - написать комментарий
     */
    protected function _checkComments(\Service\Tasks\Model_Tasks_Task $task)
    {

        //дальше будем использовать токен пользователя поэтому сначала проверим его
        if ($errorText = $this->isBadUserAccessToken()) {
            return $this->_response->setJson(['success' => false, 'errorText' => $errorText]);
        }

        //сначала проверим доступность поста
//        if ($errorText = $this->checkPost($task)) {
//            $this->taskStop($task, $errorText);
//            return $this->_response->setJson(['success' => false, 'errorText' => $errorText, 'removeTask' => true]);
//        }


        $arr = \Service\Posting\Model_Config::GetEmoji();

        //Получам комментарии задания
        $comments = $task->getComments();

        foreach ($comments as $id => $comment) {
            $comments[$id] = strtr($comment, $arr[0]['old']);
        }

        //получаем список стоп слов
        $black = explode(' ', strtolower(file_get_contents(ENGINE_PATH . 'static/black.txt')));

        //Получаем комментарии в записимости от типа задания
        switch ($task->vkType) {
            case 'post':
                $response = $this->VK->getPostComments($task->ownerId, $task->itemId, $task->ownerType, $this->user_access_token);
                break;
            case 'photo':
                $response = $this->VK->getPhotoComments($task->ownerId, $task->itemId, $task->ownerType, $this->user_access_token);
                break;
            case 'video':
                $response = $this->VK->getVideoComments($task->ownerId, $task->itemId, $task->ownerType, $this->user_access_token);
                break;
        }

        if (!empty($response['error'])) {
            return $this->_response->setJson(['success' => false, 'errorText' => "Ошибка {$response['error']} {$response['errorText']}"]);
        }

        //Если не удалось получить комментарии вернем ошибку
        if (empty($response['items'])) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Не удается получить комментарии']);
        }

        $currentComment = null;

        //Перебираем полученные комментарии ищем поьлзователя среди оставивших комментарий
        foreach ($response['items'] as $comment) {
            if ($comment['from_id'] == $this->_application->User->uid) {
                $currentComment = $comment;
            }
        }

        //Если пользователь не найден
        if ($currentComment === null) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Вы не оставляли комментарий']);
        }

        //Если нужно оставить определенный в задании комментарий
        if ($task->commentType === 3) {

            //Если оставленный комментарий соотвествует заданному в задании комментарию
            if (in_array($currentComment['text'], $comments) || isset($currentComment['text'])) {

                //Задание выполнено
                $this->taskSuccess($task);

                //Возвращаем успеный ответ
                return $this->_response->setJson([
                    'success' => true,
                    'message' => 'Комментарий выполнен!',
                    'karma' => $this->_application->User->karma,
                    'karmaText' => number_format($this->_application->User->karma, 1),
                    'balance' => number_format($this->_application->User->balance, 1, '.', ' '),
                ]);
            } else {
                //Если оставленный комментарий НЕ соотвествует заданному в задании комментарию
                return $this->_response->setJson([
                    'success' => false,
                    'errorText' => 'Комментарий не соответствует заданному списку',
                ]);
            }
        } //Если нужно оставить любой комментарий
        elseif ($task->commentType == 2) {

            //Задание выполнлено
            $this->taskSuccess($task);

            //Возвращаем успеный ответ
            return $this->_response->setJson([
                'success' => true,
                'message' => 'Комментарий выполнен!',
                'karma' => $this->_application->User->karma,
                'karmaText' => number_format($this->_application->User->karma, 1),
                'balance' => number_format($this->_application->User->balance, 1, '.', ' '),
            ]);

        } else {

            $words = explode(' ', $currentComment['text']);
            $found = false;

            foreach ($words as $word) {
                if (in_array(strtolower($word), $black)) {
                    $found = true;
                }
            }

            if (!$found) {

                $this->taskSuccess($task);

                return $this->_response->setJson([
                    'success' => true,
                    'message' => 'Комментарий выполнен!',
                    'karma' => $this->_application->User->karma,
                    'karmaText' => number_format($this->_application->User->karma, 1),
                    'balance' => number_format($this->_application->User->balance, 1, '.', ' '),
                ]);
            } else {
                return $this->_response->setJson([
                    'success' => false,
                    'errorText' => 'Комментарий должен быть ' . ($task->commentType == 0 ? 'нейтральным' : 'положительным'),
                ]);
            }
        }
    }

    /*
     * Проверка задания - опросы
     */
    protected function _checkPolls(\Service\Tasks\Model_Tasks_Task $task)
    {

        //дальше будем использовать токен пользователя поэтому сначала проверим его
        if ($errorText = $this->isBadUserAccessToken()) {
            return $this->_response->setJson(['success' => false, 'errorText' => $errorText]);
        }

        //пробуем получить пост
        $response = $this->VK->getPost($task->ownerId, $task->itemId, $task->ownerType, $this->user_access_token);

        if ($response === []) {
            $errorText = 'Невозможно выполнить задание! Объект не найден!';
        }

        //обработаем ошибки
        if (!empty($response['error'])) {
            $errorText = "Ошибка {$response['error']} {$response['errorText']}";
        }

        if (!empty($errorText)) {
            $this->taskStop($task, $errorText);
            return $this->_response->setJson(['success' => false, 'errorText' => $errorText, 'removeTask' => true]);
        }


        //для ананимных опросов (просто замеряем скошлько было проголосовавших до и после выполнения задания)
        if ($task->isAnonymous) {

            $taskUser = $this->factoryTasks->users->getByTaskIdUserId($task->taskId, $this->_application->User->userId, true);


            $post = $response[0];
            foreach ($post['attachments'] as $attachment) {

                if ($attachment[$attachment['type']]['id'] == $task->pollId) {

                    if ($taskUser->votes < intval($attachment['poll']['votes'])) {

                        $this->taskSuccess($task);

                        return $this->_response->setJson([
                            'success' => true,
                            'message' => 'Голосование выполнено!',
                            'karma' => $this->_application->User->karma,
                            'karmaText' => number_format($this->_application->User->karma, 1),
                            'balance' => number_format($this->_application->User->balance, 1, '.', ' '),
                        ]);
                    }
                }
            }


            return $this->_response->setJson(['success' => false, 'errorText' => 'Вы не проголосовали']);
        }


        //на случай если по какой то причине в задании нет answerId и answerIds то получим их (нужно разобраться почему так происходит!)
        if (!$task->answerId and !$task->answerIds) {

            if (!empty($response['answers']) and is_array($response['answers'])) {
                foreach ($response['answers'] as $answer) {
                    $answerIds[] = $answer['id'];
                }
            }

            if (is_array($answerIds) && count($answerIds) > 0) {
                $task->makeShadow();
                $task->answerIds = implode(',', $answerIds);
                $this->factoryTasks->tasks->save($task);
            }

        }


        if ($task->answerId) {
            $answerId = $task->answerId; //если нужно голосовать за определенный вариант
        } else {
            $answerIds = explode(',', $task->answerIds);
            $answerId = array_shift($answerIds); //берем первый вариант ответа
        }


        //проголосуем с нашего определенного аккаунта
        //далее будем проверять голосование пользователя с этим же токеном (данные о проголосовавших пользователях можно получить только проголосовавшим аккаунтом!)
        $check_access_token = $this->_application->getRandomCheckToken();
        $response = $this->VK->addVote($task->ownerId, $task->ownerType, $task->pollId, $answerId, $check_access_token);
        //возвращает Access to poll denied непонятно почему(

        if (!empty($response['error'])) {
            return $this->_response->setJson(['success' => false, 'errorText' => "Ошибка {$response['error']} {$response['errorText']}"]);
        }

        $response = $this->VK->getVoters($task->ownerId, $task->ownerType, $task->pollId, $task->answerId ?: $task->answerIds, $check_access_token);

        //это еще для чего???
//        if ($response['error'] && $response['error'] == 250) {
//
//            $this->taskSuccess($task);
//
//            return $this->_response->setJson([
//                'success' => true,
//                'message' => 'Голосование выполнено!',
//                'karma' => $this->_application->User->karma,
//                'karmaText' => number_format($this->_application->User->karma, 1),
//                'balance' => number_format($this->_application->User->balance, 1, '.', ' '),
//            ]);
//        }

        if (!empty($response['error'])) {
            return $this->_response->setJson(['success' => false, 'errorText' => "Ошибка {$response['error']} {$response['errorText']}"]);
        }


        foreach ($response as $answer) {

            if ($answer['answer_id'] == $task->answerId || $task->answerId == 0) {

                if (in_array($this->_application->User->uid, $answer['users']['items'])) {

                    $this->taskSuccess($task);

                    return $this->_response->setJson([
                        'success' => true,
                        'message' => 'Голосование выполнено!',
                        'karma' => $this->_application->User->karma,
                        'karmaText' => number_format($this->_application->User->karma, 1),
                        'balance' => number_format($this->_application->User->balance, 1, '.', ' '),
                    ]);

                } elseif ($task->answerId > 0) {
                    return $this->_response->setJson([
                        'success' => false,
                        'errorText' => 'Вы проголосовали не за тот вариант',
                    ]);
                }
            }
        }

        return $this->_response->setJson(['success' => false, 'errorText' => 'Ваш голос не найден']);
    }


    /*
     * Проверка задания - просмотры постов
     */
    protected function _checkViews(\Service\Tasks\Model_Tasks_Task $task)
    {

        //дальше будем использовать токен пользователя поэтому сначала проверим его
        if ($errorText = $this->isBadUserAccessToken()) {
            return $this->_response->setJson(['success' => false, 'errorText' => $errorText]);
        }

        //получаем пост
        $response = $this->VK->getPost($task->ownerId, $task->itemId, $task->ownerType, $this->user_access_token);

        if ($response === []) {
            $errorText = 'Невозможно выполнить задание! Объект не найден!';
        }

        //обработаем ошибки
        if (!empty($response['error'])) {
            $errorText = "Ошибка {$response['error']} {$response['errorText']}";
        }

        if (!empty($errorText)) {
            $this->taskStop($task, $errorText);
            return $this->_response->setJson(['success' => false, 'errorText' => $errorText, 'removeTask' => true]);
        }

        //получаем количество просмотров поста
        $countViews = $response[0]['views']['count'];

        //получаем выполненное задание пользователя
        $taskUser = $this->factoryTasks->users->getByTaskIdUserId($task->taskId, $this->_application->User->userId,
            true);

        if ($countViews == $taskUser->countViews) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Задание не выполнено. Не изменилось количество просмотров поста!']);
        }

        //если количество просмотров увеличилось с начала выполнения задания
        // и прошло больше 5 секунд с начала выполнения задания - засчитываем просмотр
        if ($taskUser->countViews < $countViews
            and time() - $taskUser->views > 5
            and $taskUser->views > strtotime('-1 DAY')) {

            $this->taskSuccess($task);

            return $this->_response->setJson([
                'success' => true,
                'message' => 'Просмотр записи выполнен!',
                'karma' => $this->_application->User->karma,
                'karmaText' => number_format($this->_application->User->karma, 1),
                'balance' => number_format($this->_application->User->balance, 1, '.', ' '),
            ]);
        }

        return $this->_response->setJson([
            'success' => false,
            'errorText' => 'Необходимо просмотреть запись не менее 5 секунд',
        ]);
    }

    /*
     * Проверка задания - просмотры видео
     */
    protected function _checkVideo(\Service\Tasks\Model_Tasks_Task $task)
    {

        //дальше будем использовать токен пользователя поэтому сначала проверим его
        if ($errorText = $this->isBadUserAccessToken()) {
            return $this->_response->setJson(['success' => false, 'errorText' => $errorText]);
        }


        $response = $this->VK->getVideo($task->ownerId, $task->itemId, $task->ownerType, $this->user_access_token);

        if (!empty($response['error'])) {
            return $this->_response->setJson(['success' => false, 'errorText' => "Ошибка {$response['error']} {$response['errorText']}"]);
        }

        //если не удалось получить видео
        if (empty($response['items'])) {
            $this->taskStop($task, 'Видео недоступно!');
            return $this->_response->setJson(['success' => false, 'errorText' => 'Извините, видео не найдено!', 'removeTask' => true]);
        }

        //получаем количество просмотров видео
        $countViews = $response['items'][0]['views'];

        //получаем выполненное задание пользователя
        $taskUser = $this->factoryTasks->users->getByTaskIdUserId($task->taskId, $this->_application->User->userId,
            true);

        if ($countViews == $taskUser->countViews) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Задание не выполнено. Не изменилось количество просмотров видео!']);
        }

        //если количество просмотров увеличилось с начала выполнения задания
        // и прошло больше 5 секунд с начала выполнения задания - засчитываем просмотр
        if ($taskUser->countViews < $countViews
            and time() - $taskUser->views > 5
            and $taskUser->views > strtotime('-1 DAY')) {

            //задание выполнено
            $this->taskSuccess($task);

            return $this->_response->setJson([
                'success' => true,
                'message' => 'Просмотр видео выполнен!',
                'karma' => $this->_application->User->karma,
                'karmaText' => number_format($this->_application->User->karma, 1),
                'balance' => number_format($this->_application->User->balance, 1, '.', ' '),
            ]);
        }

        return $this->_response->setJson([
            'success' => false,
            'errorText' => 'Необходимо просмотреть видео не менее 5 секунд',
        ]);
    }

    /*
    * Вызываем при успешно выполненном задании
    * Здесь начисляем бонус, баланс, ставим нужные флаги, создаем сообщение о выполненном задании и тд
    */
    protected function taskSuccess(Model_Tasks_Task $task)
    {
        $field = 'price_' . $task->type . '_sell' . ($this->_application->User->karma < 0 ? '_negative' : '') . ($this->_application->User->karma >= 75 ? '_positive' : '');

        $task = $this->factoryTasks->tasks->getById($task->taskId, true);
        $task->countReady++;
        $task->countMinute++;
        $task->count10Min++;
        $task->countHour++;
        $task->countDay++;
        $task->countRemain = $task->count - $task->countReady;
        $this->factoryTasks->tasks->save($task);

        if ($task->isSpecial && $task->isSpecialInvite && $task->type == 'join') {
            $special = $this->factoryTasks->specialGroups->getById($task->specialId);
            $specialUser = $this->factoryTasks->specialGroups->users->getNew();

            //$special иногда прилетает null пришлось сделать так (скорее всего так делать нельзя)
            if ($special) {
                $specialUser->specialId = $special->groupId;
            } else {
                $specialUser->specialId = $task->specialId;
            }

            $specialUser->userId = $this->_application->UserID;
            $specialUser->dateCreate = time();
            $this->factoryTasks->specialGroups->users->save($specialUser);
        }

        $taskUser = $this->factoryTasks->users->getByTaskIdUserId($task->taskId, $this->_application->UserID, true);

        if ($taskUser === null) {
            $taskUser = $this->factoryTasks->users->getNew();
            $taskUser->taskId = $task->taskId;
            $taskUser->type = $task->type;
            $taskUser->userId = $this->_application->UserID;
            $taskUser->uid = $this->_application->User->uid;
            $taskUser->isDel = false;
            $taskUser->isActive = true;
        }
        $taskUser->isDone = true;
        $taskUser->isDoneDate = time();
        $this->factoryTasks->users->save($taskUser);

        $this->_application->User->makeShadow();

        if ($this->_fields[$task->type]['limit']) {
            $this->_application->User->{$task->type . 'CountDay'}++;
            $this->_application->User->{$task->type . 'CountHour'}++;
            $this->_application->User->{$task->type . 'Count10Min'}++;
            $this->_application->User->{$task->type . 'CountMinute'}++;
        }

        $balance = $this->factoryUsers->users->balance->getNew();
        $balance->userId = $this->_application->User->userId;
        $balance->isTask = true;
        $balance->balance = floatval($this->_application->settings[$field]);
        $balance->balanceFrom = $this->_application->User->balance;
        $this->_application->User->balance += $this->_application->settings[$field];
        $balance->balanceTo = $this->_application->User->balance;
        $balance->dateCreate = time();
        $balance->comment = $this->_fields[$task->type]['comment'];

        $karma = json_decode(file_get_contents(\Service\Users\Model_Config::$karmaPath), true);
        $karmaObj = $this->factoryUsers->users->karma->getNew();
        $karmaObj->userId = $this->_application->UserID;
        $karmaObj->karma = floatval($karma['karma'][$task->type . ($this->_application->User->karma < 0 ? '_negative' : '')]);
        $karmaObj->karmaFrom = $this->_application->User->karma;
        $this->_application->User->karma += $karma['karma'][$task->type . ($this->_application->User->karma < 0 ? '_negative' : '')];

        if ($this->_application->User->karma > 100.00) {
            $this->_application->User->karma = 100.00;
        }
        $karmaObj->karmaTo = $this->_application->User->karma;
        $karmaObj->dateCreate = time();
        $karmaObj->comment = $this->_fields[$task->type]['comment'];

        if ($this->factoryUsers->users->save($this->_application->User)) {
            $this->factoryUsers->users->karma->save($karmaObj);
            $this->factoryUsers->users->balance->save($balance);

            if ($this->_application->User->parentId > 0) {
                $settingsRef = json_decode(file_get_contents(\Service\Users\Model_Config::$referrersPath), true);
                $parent = $this->factoryUsers->users->getById($this->_application->User->parentId, true);
                $balanceParent = $this->factoryUsers->users->balance->getNew();
                $balanceParent->userId = $parent->userId;
                $balanceParent->isReferrer = true;
                $balanceParent->balance = floatval($this->_application->settings[$field]) * $settingsRef['percent']['parentId']['tasks'] / 100;
                $balanceParent->balanceFrom = $parent->balance;
                $parent->balance += $balanceParent->balance;
                $balanceParent->balanceTo = $parent->balance;
                $balanceParent->dateCreate = time();
                $balanceParent->comment = 'Реферал ' . $this->_fields[$task->type]['comment'];

                if ($this->factoryUsers->users->save($parent)) {
                    $this->factoryUsers->users->balance->save($balanceParent);
                }
            }

            if ($this->_application->User->pParentId > 0) {
                $settingsRef = json_decode(file_get_contents(\Service\Users\Model_Config::$referrersPath), true);
                $pParent = $this->factoryUsers->users->getById($this->_application->User->pParentId, true);
                $balancepParent = $this->factoryUsers->users->balance->getNew();
                $balancepParent->userId = $pParent->userId;
                $balancepParent->isReferrer = true;
                $balancepParent->balance = floatval($this->_application->settings[$field]) * $settingsRef['percent']['pParentId']['tasks'] / 100;
                $balancepParent->balanceFrom = $pParent->balance;
                $pParent->balance += $balancepParent->balance;
                $balancepParent->balanceTo = $pParent->balance;
                $balancepParent->dateCreate = time();
                $balancepParent->comment = 'Реферал 2 уровня ' . $this->_fields[$task->type]['comment'];

                if ($this->factoryUsers->users->save($pParent)) {
                    $this->factoryUsers->users->balance->save($balancepParent);
                }
            }
        }

        if ($this->_application->User->bonus && $this instanceof Controller_State_Client_List_Bonus) {
            $this->_application->User->makeShadow();
            $bonus = \Service\Users\Model_Config::GetBonusSettings();
            $mConfig = \Service\Messages\Model_Config::GetConfig();

            $balanceBonus = $this->factoryUsers->users->balance->getNew();
            $balanceBonus->userId = $this->_application->User->userId;
            $balanceBonus->isBonus = true;

            if ($this->_application->User->bonus == 1) {
                $balanceBonus->comment = 'Бонус за регистрацию';
                $balanceBonus->balance = floatval($bonus['register']);
            } elseif ($this->_application->User->bonus == 2) {
                $balanceBonus->comment = 'Бонус за выполнение ежедневного задания';
                $balanceBonus->balance = floatval($bonus['day_one']);
            }

            $balanceBonus->balanceFrom = $this->_application->User->balance;
            $this->_application->User->balance += $balanceBonus->balance;
            $balanceBonus->balanceTo = $this->_application->User->balance;
            $balanceBonus->dateCreate = time();

            if ($this->factoryUsers->users->balance->save($balanceBonus)) {
                $this->_application->User->bonus = 0;
                $this->factoryUsers->users->save($this->_application->User);
            }

            $message = $this->factoryMessages->users->getNew();
            $message->userId = $this->_application->User->userId;
            $message->isDone = false;
            $message->type = \Service\Messages\Model_Config::TYPE_SYSTEM;

            if ($this->_application->User->bonus == 1) {
                $text = $mConfig['bonus']['types']['regiter']['text'];
                $text = str_replace('%balance%', $balanceBonus->balance, $text);
            } else {
                $text = $mConfig['bonus']['types']['day_task']['text'];
                $text = str_replace('%balance%', $balanceBonus->balance, $text);
            }

            $message->text = $text;
            $message->icon = 'bonus';
            $this->factoryMessages->users->save($message);
        }

        return true;
    }

    protected function isBadUserAccessToken()
    {

        //если не указан токен пользователя
        if ($this->user_access_token === '') {
            $errorText = 'Для выполнения заданий укажите ваш токен в настройках профиля!';
        } else {
            //проверяем валидность токена
            $response = $this->VK->checkUserAccessToken($this->user_access_token);
            if (!empty($response['error']) and $response['error'] == 5) {
                $errorText = 'Ваш токен устарел! Для выполнения заданий обновите ваш токен в настройках профиля!';
            }
        }

        if (!empty($errorText)) return $errorText;
        return false;
    }

    private function _getTaskHTML()
    {
        if ($this->_params['type'] == 'all') {
            $list = [];

            foreach ($this->_types as $type => $title) {

                if ($this->_application->User->balance < 0 && !in_array($type, ['views', 'video', 'polls'])) continue;

                $list = array_merge($list, $this->factoryTasks->tasks->getListSpecials($this->_application->User, $type, 1, 0));
                $limit = 10;

                if ($limit > 0) {
                    $arr = $this->factoryTasks->tasks->getItemsList($this->_application->User, $type, $limit, 0);
                    $list = array_merge($list, $arr);
                }
            }
        } else {
            if ($this->_application->User->balance < 0 && !in_array($this->_params['type'], ['views', 'video', 'polls'])) {
                $this->_errors[] = 'Пока ваш баланс имеет отрицательное значение, вам не доступно выполнение этого типа заданий. Что бы вывести баланс в положительное значение и получить доступ ко всем заданиям, выполняйте задания, которые невозможно отменить, или приобретите пакет баллов перекрывающий ваш минус.';
                $list = [];
            } else {
                $list = $this->factoryTasks->tasks->getListSpecials($this->_application->User, $this->_params['type'], 1, 0);
            }

            $type = $this->_params['type'];
            $limit = 10;

            if ($limit > 0) {
                $arr = $this->factoryTasks->tasks->getItemsList($this->_application->User, $type, $limit, 0);
                $list = array_merge($list, $arr);
            }
        }

        usort($list, [$this, '_sort_list']);
        $list = array_slice($list, 0, 1);

        $task = null;

        if (count($list)) {
            $task = $list[0];
        } else {
            return '';
        }

        $taskUser = $this->factoryTasks->users->getByTaskIdUserId($task->taskId, $this->_application->UserID, true);

        if ($taskUser === null) {
            $taskUser = $this->factoryTasks->users->getNew();
            $taskUser->taskId = $task->taskId;
            $taskUser->type = $task->type;
            $taskUser->userId = $this->_application->UserID;
            $taskUser->uid = $this->_application->User->uid;
            $taskUser->isDel = false;
            $taskUser->isDone = false;
            $taskUser->isActive = true;
            $this->factoryTasks->users->save($taskUser);
        } else {
            $taskUser->isActive = true;
            $this->factoryTasks->users->save($taskUser);
        }

        $vars = [
            'karmaParams' => $this->karmaParams,
            'errors' => $this->_errors,
            'user' => $this->_application->User,
            'prices' => $this->_application->settings,
            'type' => $this->_params['type'],
            'titles' => $this->_titles,
            'task' => $task,
            'types' => $this->_types,
        ];

        return \STPL::Fetch('client/task', $vars);
    }

    private function _taskPrepare()
    {

        $task = $this->factoryTasks->tasks->getById($this->_request->post['taskId']->int());

        if ($task === null) return $this->_response->setJson(['success' => false]);

        $taskUser = $this->factoryTasks->users->getByTaskIdUserId($task->taskId, $this->_application->UserID);

        if ($taskUser === null) {
            return $this->_response->setJson(['success' => false]);
        }

        switch ($task->type) {
            case 'comments':
                if ($task->commentType == 3) {
                    $comments = $task->getComments();
                    shuffle($comments);
                    $html = \STPL::Fetch('client/comments/comment',
                        ['taskId' => $task->taskId, 'comment' => $comments[0]]);

                    return $this->_response->setJson(['success' => true, 'action' => 'dialog', 'html' => $html]);
                }

                return $this->_response->setJson(['success' => true, 'action' => 'do']);
            case 'views':
                $response = $this->VK->getPost($task->ownerId, $task->itemId, $task->ownerType, $this->check_access_token);

                exit;
            default:
                return $this->_response->setJson(['success' => true, 'action' => 'do']);
        }
    }

    private function _abuse()
    {

        $taskId = $this->_request->post['taskId']->int(0);
        $reason = $this->_request->post['reason']->int(0);
        $comment = $this->_request->post['comment']->string('', \System\HttpRequest::OUT_HTML);

        $task = $this->factoryTasks->tasks->getById($taskId);

        if ($task === null) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Задание не найдено']);
        }

        $abuse = $this->factoryTasks->abuses->getByTaskIdUserId($task->taskId, $this->_application->User->userId);

        if ($abuse !== null) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Вы уже жаловались на это задание']);
        }

        $abuse = $this->factoryTasks->abuses->getNew();
        $abuse->taskId = $task->taskId;
        $abuse->userId = $this->_application->User->userId;
        $abuse->reason = $reason;
        $abuse->comment = $comment;
        $abuse->dateCreate = time();

        if ($this->factoryTasks->abuses->save($abuse)) {
            $taskUser = $this->factoryTasks->users->getByTaskIdUserId($task->taskId, $this->_application->User->userId,
                true);

            if ($taskUser !== null) {
                $taskUser->isDel = true;
                $taskUser->isDelDate = time();
                $this->factoryTasks->users->save($taskUser);
            }

            //автоматическая обработка жалоб
            //проверяем доступность заданий и тд смотри скрипт!
            //shell_exec(ENGINE_PATH . 'shell.sh action=Tasks/Abuse:Check abuseId=' . $abuse->abuseId . ' > /dev/null 2>/dev/null &');
        }


        return $this->_response->setJson(['success' => true, 'html' => $this->_getTaskHTML()]);
    }

    //проверка качества аккаунта ВК - запускается пользователем
    private function _unban()
    {
        if ($this->_application->User->lastCheck < time() - 60 * 10) {
            $this->_application->updateUserFromVK();

            return null;
        } else {
            $this->_errors[] = 'Проверку можно производить не чаще, чем раз в 10 минут';

            return null;
        }
    }

    /**
     * @return HttpResponse
     */
    private function _botForm()
    {
        if ($this->_application->User->token_require) {
            return $this->_response->setJson([
                'success' => true,
                'token' => true,
                'html' => \STPL::Fetch('client/forms/bot_token',
                    ['token_require' => $this->_application->User->token_require]),
            ]);
        }

        if (!$this->_application->User->access_token || ($this->_application->User->access_token_expire !== null && $this->_application->User->access_token_expire < time())) {
            return $this->_response->setJson([
                'success' => true,
                'token' => true,
                'html' => \STPL::Fetch('client/forms/bot_token'),
            ]);
        }

        return $this->_response->setJson(['success' => true, 'html' => \STPL::Fetch('client/forms/bot')]);
    }

    private function _isBotActive()
    {
        $token_requre = false;

        if ($this->_application->User->token_require) {
            $token_requre = true;
        }

        if (!$this->_application->User->access_token || ($this->_application->User->access_token_expire != null && $this->_application->User->access_token_expire < time())) {
            $token_requre = true;
        }

        $access_token = $this->_request->post['access_token']->string('');

        if ($token_requre && !$access_token) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Необходимо указать токен']);
        }

        $this->_application->User->makeShadow();
        $this->_application->User->isBot = array_sum(array_keys(Model_Config::$botTypes));
        $this->factoryUsers->users->save($this->_application->User);

        return $this->_response->setJson(['success' => true, 'html' => \STPL::Fetch('client/forms/bot_success')]);
    }

    /**
     * @param Model_Tasks_Task $a
     * @param Model_Tasks_Task $b
     */
    protected function _sort_list($a, $b)
    {
        if ($a->isSpecial && $b->isSpecial) {
            return $a->dateCreate < $b->dateCreate;
        } elseif ($a->isSpecial) {
            return -1;
        } elseif ($b->isSpecial) {
            return 1;
        }

        if ($a->prior && $b->prior) {
            return $a->dateCreate < $b->dateCreate;
        } elseif ($a->prior) {
            return -1;
        } elseif ($b->prior) {
            return 1;
        }

        return $a->dateCreate < $b->dateCreate;
    }

    /*
     * Проверяет закрыт ли профиль пользователя
     */
    protected function profileIsClosed($user_id)
    {
        $response = $this->VK->getUser($user_id, $this->check_access_token);
        return $response['is_closed'];
    }
}
