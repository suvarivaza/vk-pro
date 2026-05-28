<?php

//INFO: Обновляет лимиты выполнения заданий

namespace Service\Users;

class Controller_Shell_Counts extends \System\Service_Controller_Shell
{
    public function A_ClearDay()
    {
        $this->factoryUsers->users->clearLimits('Day', 'pollsCount', 'joinCount', 'friendsCount', 'likesCount',
            'repostsCount', 'commentsCount');
        $this->factoryTasks->tasks->clearLimits('countDay');
    }

    public function A_ClearHour()
    {
        $this->factoryUsers->users->clearLimits('Hour', 'pollsCount', 'joinCount', 'friendsCount', 'likesCount',
            'repostsCount', 'commentsCount');
        $this->factoryTasks->tasks->clearLimits('countHour');
    }

    public function A_Clear10Min()
    {
        $this->factoryUsers->users->clearLimits('10Min', 'pollsCount', 'joinCount', 'friendsCount', 'likesCount',
            'repostsCount', 'commentsCount');
        $this->factoryTasks->tasks->clearLimits('count10Min');
    }

    public function A_ClearMinute()
    {
        $this->factoryUsers->users->clearLimits('Minute', 'pollsCount', 'joinCount', 'friendsCount', 'likesCount',
            'repostsCount', 'commentsCount');
        $this->factoryTasks->tasks->clearLimits('countMinute');
    }
}
