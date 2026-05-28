<?php

namespace Service\News;

use System\Service_Controller_Router_Rewrite;

class Controller_Router extends Service_Controller_Router_Rewrite
{
    protected $routes = [
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
        'State_Admin_Delete' => [
            'regexp' => '@^admin/delete/(.*)$@',
            'matches' => [
                1 => 'alias',
            ],
            'admin' => true,
        ],
        'State_Admin_List' => [
            'regexp' => '@^admin/list/(\d+)$@',
            'matches' => [
                1 => 'page',
            ],
            'admin' => true,
        ],
        'State_Admin_Settings' => [
            'string' => 'admin/settings',
            'admin' => true,
        ],
        'State_Admin_Default' => [
            'string' => 'admin/',
            'admin' => true,
        ],

        'State_Client_Default' => [
            'string' => '',
        ],

        'State_Client_Detail' => [
            'regexp' => '@(.*)@',
            'matches' => [
                '1' => 'path',
            ],
        ],
    ];
}
