<?php

//INFO: Контроллер обрабатывающий переход по ссылке при выполнении задания

namespace Service\Tasks;

class Controller_State_Client_Go extends Controller_State_Client
{
    private $userLimits = null;

    public function actionGet()
    {

        $this->userLimits = json_decode(file_get_contents(Model_Config::$limitsPath), true);


        //Если пользователь не авторизован венем ответ сервера 404
        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }

        //Если не передан taskId вернем 404
        $taskId = $this->_request->get['taskId']->int(0);
        if (!$taskId) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }

        //Получаем задание
        $task = $this->factoryTasks->tasks->getById($taskId);

        if (!in_array($task->type, ['views', 'video'])) {

            $countDay = $task->type . 'CountDay';
            $countHour = $task->type . 'CountHour';
            $count10Min = $task->type . 'Count10Min';

            if ($this->_application->User->{$countDay} >= intval($this->userLimits['user'][$task->type]['day'])) {
                $_SESSION[$task->type]['countDay'] = true;
                return $this->_response->setBody('<script>window.close();</script>');
            }

            if ($this->_application->User->{$countHour} >= intval($this->userLimits['user'][$task->type]['hour'])) {
                $_SESSION[$task->type]['countHour'] = true;
                return $this->_response->setBody('<script>window.close();</script>');
            }

            if ($this->_application->User->{$count10Min} >= intval($this->userLimits['user'][$task->type]['interval'])) {
                $_SESSION[$task->type]['count10Min'] = true;
                return $this->_response->setBody('<script>window.close();</script>');
            }
        }

        //Если задание не активно
        if (!$task->active) {
            $_SESSION[$task->taskId]['not_active'] = true;
            //return $this->_response->setBody('<script>window.close();</script>');
        }


        //Получаем задание пользователя (если оно уже было создано ранее)
        $taskUser = $this->factoryTasks->users->getByTaskIdUserId($task->taskId, $this->_application->User->userId,
            true);

        //Здесь создается задание пользователя
        if (!$taskUser) {
            $taskUser = $this->factoryTasks->users->getNew();
            $taskUser->taskId = $task->taskId;
            $taskUser->type = $task->type;
            $taskUser->userId = $this->_application->UserID;
            $taskUser->uid = $this->_application->User->uid;
            $taskUser->isDel = false;
            $taskUser->isActive = true;
            $taskUser->isDone = false;
            $taskUser->isDoneDate = null;
            $this->factoryTasks->users->save($taskUser);
            $taskUser->makeShadow();
        }


        //userType = 1 это админ! у всех остальных юзеров userType = 0
        if ($taskUser->isDone && !$this->_application->User->userType) {
            return $this->_response->setBody('<script>window.close();</script>');
        }


        if (in_array($task->type, ['likes','reposts','comments','views', 'polls']) and $task->vkType != 'comment') {

            //pd($task);
//            pd($this->_application->User->access_token);

            //перед переходом к выполнению задания проверяем доступность поста
            //$response = $this->VK->getPost($task->ownerId, $task->itemId, $task->ownerType, $this->check_access_token);
            $response = $this->VK->getPost($task->ownerId, $task->itemId, $task->ownerType, $this->_application->User->access_token);


            //если не удалось получить пост закрываем вкладку - инициализируется проверка задания
            if (!empty($response['error'] or $response === [])) {
//                pd($response);
//                pd([
//                    'response' => $response,
//                    'task' => $task,
//                    'check_access_token' => $this->check_access_token
//                ]);
                return $this->_response->setBody('<script>window.close();</script>');
            }

            if ($task->type == 'views') {
                $taskUser->countViews = $response[0]['views']['count']; //сохраним количество просмотров поста ДО выполнения задания
                $taskUser->views = time(); //сохраним текущюю временную метку
                $this->factoryTasks->users->save($taskUser);
            }


            //Для опросов с isAnonymous
            if ($task->type == 'polls') {

                $post = $response[0];

                foreach ($post['attachments'] as $attachment) {
                    if ($attachment['poll']['id'] == $task->pollId) {
                        $taskUser->votes = $attachment['poll']['votes']; //запишем количество проголосовавших ДО выполнения задания (для анонимных голосований)
                        $this->factoryTasks->users->save($taskUser);
                    }
                }
            }

            //переопределяем ссылку на пост
            $task->url = "https://vk.com/wall". $task->ownerId . '_' . $task->itemId;
        }

        else if ($task->type == 'video') {

            //перед переходом к выполнению задания проверяем доступность видео
            //$response = $this->VK->getVideo($task->ownerId, $task->itemId, $task->ownerType, $this->check_access_token);
            $response = $this->VK->getVideo($task->ownerId, $task->itemId, $task->ownerType, $this->_application->User->access_token);

            //если не удалось получить видео закрываем вкладку - инициализируется проверка задания
            if (empty($response['items'])) return $this->_response->setBody('<script>window.close();</script>');

            $taskUser->countViews = $response['items'][0]['views']; //количество просмотров видео до выполнения задания
            $taskUser->views = time();
            $this->factoryTasks->users->save($taskUser);

            //переопределяем ссылку на видео
            $task->url = "https://vk.com/video". $task->ownerId . '_' . $task->itemId;
        }

        else if ($task->type == 'friends') {
            $task->url = "https://vk.com/id". $task->ownerId; //переопределяем ссылку на пользователя
        }

        else if ($task->type == 'join') {

            //Проверяем сразу доступность получения участников группы
            //потому что есть группы у которых скрыты участники!
            //$this->VK->getMembers($task->ownerId, $this->check_access_token);

//            $response = $this->VK->getGroup($task->ownerId, $this->check_access_token);
            $response = $this->VK->getGroup($task->ownerId, $this->_application->User->access_token);

            //если нет доступа к подписчикам группы закрываем вкладку - инициализируем переход обратно в задания
            if (!empty($response['error'])) return $this->_response->setBody('<script>window.close();</script>');

            $group_id = abs($task->ownerId);
            $task->url = "https://vk.com/public{$group_id}"; //переопределяем ссылку на группу
        }



        return $this->_response->setLocation($task->url);
    }
}
