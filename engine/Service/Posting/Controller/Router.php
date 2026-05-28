<?php

namespace Service\Posting;

class Controller_Router extends \System\Service_Controller_Router_Rewrite
{
    protected $routes = [
        'State_Client_Edit' => [
            'regexp' => '@^(\d+)/edit/(\d+)$@',
            'matches' => [
                1 => 'groupId',
                2 => 'postId',
            ],
        ],
        'State_Client_Add' => [
            'string' => 'add',
        ],
        'State_Client_List_Date' => [
            'regexp' => '@^(\d+)/(\d{2}\.\d{2}\.\d{4})$@',
            'matches' => [
                1 => 'groupId',
                2 => 'date',
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
