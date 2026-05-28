<?php

namespace Service\Grabber;

class Controller_Router extends \System\Service_Controller_Router_Rewrite
{
    protected $routes = [
        'State_Client_Add' => [
            'regexp' => '@^(\d+)/add$@',
            'matches' => [
                1 => 'groupId',
            ],
        ],
        'State_Client_List' => [
            'regexp' => '@^(\d+)$@',
            'matches' => [
                1 => 'groupId',
            ],
        ],
        'State_Client_Default' => [
            'regexp' => '@(.*)@',
            'matches' => [
                '1' => 'path',
            ],
        ],
    ];
}
