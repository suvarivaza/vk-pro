<?php

namespace Service\Messages;

class Controller_Router extends \System\Service_Controller_Router_Rewrite
{
    protected $routes = [
        'State_Admin_Config' => [
            'string' => 'admin/config',
            'admin' => true,
        ],

        'State_Admin_Add' => [
            'string' => 'admin/add',
            'admin' => true,
        ],

        'State_Admin_Edit' => [
            'regexp' => '@^admin/edit/(.*)$@',
            'matches' => [
                1 => 'alias',
            ],
            'admin' => true,
        ],

        'State_Admin_List' => [
            'regexp' => '@^admin/(\d+)$@',
            'matches' => [
                1 => 'page',
            ],
            'admin' => true,
        ],

        'State_Admin_Default' => [
            'string' => 'admin/',
            'admin' => true,
        ],

        'State_Client_Ajax' => [
            'string' => 'ajax',
        ],

        /* Клиентская часть */
        'State_Client_Default' => [
            'regexp' => '@(.*)@',
            'matches' => [
                '1' => 'path',
            ],
        ],
    ];
}
