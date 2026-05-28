<?php

namespace Service\Logs;

use System\Service_Controller_Router_Rewrite;

class Controller_Router extends Service_Controller_Router_Rewrite
{
    protected $routes = [
        'State/Admin/List' => [
            'regexp' => '@^admin/list/(\d+)$@',
            'matches' => [
                1 => 'page',
            ],
        ],
        'State/Admin/Visit' => [
            'regexp' => '@^admin/visit/(\d+)$@',
            'matches' => [
                1 => 'page',
            ],
            'admin' => true,
        ],
        'State/Admin/Active' => [
            'regexp' => '@^admin/active/(\d+)$@',
            'matches' => [
                1 => 'userId',
            ],
            'admin' => true,
        ],
    ];
}
