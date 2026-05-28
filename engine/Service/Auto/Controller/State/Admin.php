<?php

namespace Service\Auto;

/**
 * Class Controller_State_Amin
 *
 * @package Service\Tasks
 */
abstract class Controller_State_Admin extends \System\Service_Controller_State
{
    protected $_limit = 50;

    protected $_types = [
        'likes' => 'Поставить лайк',
        'reposts' => 'Сделать репост',
        'comments' => 'Оставить комментарий',
        'join' => 'Подписаться',
        'polls' => 'Участвовать в опросе',
        'views' => 'Просмотреть запись',
        'video' => 'Просмотреть видео',
    ];
}
