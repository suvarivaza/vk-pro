<?php

namespace Service\Faq;

use System\Service_Controller_Router_Rewrite;

class Controller_Router extends Service_Controller_Router_Rewrite
{
    protected $routes = [
        'State_Admin_List' => [
            'regexp' => '@^admin/list/(\d+)$@',
            'matches' => [
                1 => 'page',
            ],
            'admin' => true,
        ],
        'State_Admin_My' => [
            'string' => 'admin/my',
        ],
        'State_Admin_Questions_Edit' => [
            'regexp' => '@^admin/rubrics/(\d+)/edit/(\d+)$@',
            'matches' => [
                1 => 'rubricId',
                2 => 'qId',
            ],
            'admin' => true,
        ],
        'State_Admin_Questions_Add' => [
            'regexp' => '@^admin/rubrics/(\d+)/add$@',
            'matches' => [
                1 => 'rubricId',
            ],
            'admin' => true,
        ],
        'State_Admin_Questions_List_User' => [
            'regexp' => '@^admin/rubrics/(\d+)/user$@',
            'matches' => [
                1 => 'rubricId',
            ],
            'admin' => true,
        ],
        'State_Admin_Questions_List' => [
            'regexp' => '@^admin/rubrics/(\d+)/list$@',
            'matches' => [
                1 => 'rubricId',
            ],
            'admin' => true,
        ],
        'State_Admin_Rubrics_Edit' => [
            'regexp' => '@^admin/rubrics/edit/(\d+)$@',
            'matches' => [
                1 => 'rubricId',
            ],
            'admin' => true,
        ],
        'State_Admin_Rubrics_Add' => [
            'string' => 'admin/rubrics/add',
            'admin' => true,
        ],
        'State_Admin_Rubrics_List' => [
            'string' => 'admin/rubrics',
            'admin' => true,
        ],
        'State_Admin_Default' => [
            'string' => 'admin',
            'admin' => true,
        ],
        /* Клиентская часть */
        'State_Client_My' => [
            'string' => 'my',
        ],
        'State_Client_Questions' => [
            'regexp' => '@^list/(\d+)$@',
            'matches' => [
                '1' => 'rubricId',
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
