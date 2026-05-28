<?php

namespace Service\Users;


class Controller_Shell_Auto extends \System\Service_Controller_Shell
{
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

    public function A_Test()
    {
        $user = $this->factoryUsers->users->getById(2);
        $list = [];

        foreach ($this->_types as $type => $title) {
            $arr = $this->factoryTasks->tasks->getItemsList($user, $type, 1, 0, 0);
            $list = array_merge($list, $arr);
        }

        /** @var \Service\Tasks\Model_Tasks_Task $task */
        foreach ($list as $task) {

            switch ($task->type) {
                case 'likes':
                    $request = $this->VK->likesAdd($task->vkType, $task->ownerId, $task->itemId,$task->ownerType, $user->access_token);
                    sleep(rand(5, 10));
                    break;
                case 'reposts':
                    if ($task->vkType == 'post') {
                        $task->vkType = 'wall';
                    }
                    $response = $this->VK->makeRepost($task->vkType, $task->ownerId, $task->itemId, $task->ownerType,$user->access_token);

                    sleep(rand(5, 10));
                    break;
                case 'join':

                    $response = $this->VK->groupsJoin($task->ownerId, $user->access_token);

                    sleep(rand(5, 10));
                    break;
            }
        }
    }
}
