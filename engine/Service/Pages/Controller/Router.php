<?php

namespace Service\Pages;

class Controller_Router extends \System\Service_Controller_Router_Rewrite
{
    protected $routes = [
        'Admin_Prices_Fields' => [
            'regexp' => '@^admin/prices/fields/(\d+)$@',
            'matches' => [
                1 => 'PriceID',
            ],
            'admin' => true,
        ],
        'Admin_Prices_Edit' => [
            'regexp' => '@^admin/prices/edit/(\d+)$@',
            'matches' => [
                1 => 'PriceID',
            ],
            'admin' => true,
        ],
        'Admin_Prices_Add' => [
            'string' => 'admin/prices/add',
            'admin' => true,
        ],
        'Admin_Prices_List' => [
            'string' => 'admin/prices',
            'admin' => true,
        ],
        'Admin_Add' => [
            'string' => 'admin/add',
            'admin' => true,
        ],
        'Admin_Edit' => [
            'regexp' => '@^admin/edit/(.*)$@',
            'matches' => [
                1 => 'alias',
            ],
            'admin' => true,
        ],
        'Admin_Delete' => [
            'regexp' => '@^admin/delete/(.*)$@',
            'matches' => [
                1 => 'alias',
            ],
            'admin' => true,
        ],
        'Admin_List_News' => [
            'regexp' => '@^admin/news/(\d+)$@',
            'matches' => [
                1 => 'page',
            ],
            'admin' => true,
        ],
        'Admin_List_Articles' => [
            'regexp' => '@^admin/articles/(\d+)$@',
            'matches' => [
                1 => 'page',
            ],
            'admin' => true,
        ],
        'Admin_List' => [
            'regexp' => '@^admin/list/(\d+)$@',
            'matches' => [
                1 => 'page',
            ],
            'admin' => true,
        ],
        'Admin_Default' => [
            'string' => 'admin/',
            'admin' => true,
        ],

        /* Клиентская часть */
        'Client_List' => [
            'string' => '/articles',
        ],
        'Client_Price' => [
            'regexp' => '@price-(.*)@',
            'matches' => [
                '1' => 'path',
            ],
        ],
        'Client_Default' => [
            'regexp' => '@(.*)@',
            'matches' => [
                '1' => 'path',
            ],
        ],
    ];
}
