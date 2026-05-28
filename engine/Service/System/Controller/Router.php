<?php

namespace Service\System;

class Controller_Router extends \System\Service_Controller_Router_Rewrite
{
    protected $routes = [
        'Admin_Settings_Sitemap' => [
            'string' => 'admin/settings/sitemap',
            'admin' => true,
        ],
        'Admin_Settings_Robot' => [
            'string' => 'admin/settings/robot',
            'admin' => true,
        ],
        'Admin_Settings' => [
            'string' => 'admin/settings',
            'admin' => true,
        ],
        'Admin_Logs' => [
            'regexp' => '@^admin/logs/(.*)$@',
            'admin' => true,
        ],
        'Admin_Default' => [
            'string' => 'admin/',
            'admin' => true,
        ],
        'Client_MoxieManager_Api' => [
            'string' => 'moxiemanager/api',
        ],
        /* Клиентская часть */
        'Client_Default' => [
            'regexp' => '@^(.*)$@',
            'matches' => [
                '1' => 'page',
            ],
        ],
    ];
}
