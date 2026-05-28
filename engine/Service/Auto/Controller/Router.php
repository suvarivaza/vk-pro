<?php

namespace Service\Auto;

class Controller_Router extends \System\Service_Controller_Router_Rewrite
{
    protected $routes = [
        'State_Admin_Tips_Fields' => [
            'string' => 'admin/tips/fields',
            'admin' => true,
        ],
        'State_Admin_Start' => [
            'string' => 'admin/start',
            'admin' => true,
        ],
        'State_Admin_Default' => [
            'string' => 'admin',
            'admin' => true,
        ],

        'State_Client_List' => [
            'regexp' => '@^list/(\d+)$@',
            'matches' => [
                1 => 'autoId',
            ],
        ],
        'State_Client_Callback' => [
            'string' => 'callback',
        ],
        'State_Client_Settings' => [
            'string' => 'settings',
        ],

        'State_Client_Buy' => [
            'string' => 'buy',
        ],

        'State_Client_Group' => [
            'regexp' => '@^(\d+)$@',
            'matches' => [
                1 => 'groupId',
            ],
        ],

        'State_Client_Default' => [
            'string' => '',
        ],
    ];
}
