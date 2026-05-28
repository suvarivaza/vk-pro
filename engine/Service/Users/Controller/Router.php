<?php

namespace Service\Users;

class Controller_Router extends \System\Service_Controller_Router_Rewrite
{
    protected $routes = [
        'State/Client/Remind' => [
            'string' => 'remind',
        ],

        'State/Admin/Bonus' => [
            'string' => 'admin/bonus',
            'admin' => true,
        ],

        'State/Admin/Referrers/Out/Settings' => [
            'string' => 'admin/referrers/out/settings',
            'admin' => true,
        ],
        'State/Admin/Referrers/Out' => [
            'string' => 'admin/referrers/out',
            'admin' => true,
        ],
        'State/Admin/Referrers/Settings' => [
            'string' => 'admin/referrers/settings',
            'admin' => true,
        ],
        'State/Admin/Referrers' => [
            'string' => 'admin/referrers',
            'admin' => true,
        ],
        'State/Admin/Penalty' => [
            'string' => 'admin/penalty',
            'admin' => true,
        ],
        'State/Admin/Karma' => [
            'string' => 'admin/karma',
            'admin' => true,
        ],
        'State/Admin/List/Bad' => [
            'regexp' => '@^admin/list/bad/(\d+)$@',
            'matches' => [
                1 => 'page',
            ],
            'admin' => true,
        ],
        'State/Admin/List/AccessToken' => [
            'regexp' => '@^admin/list/access_token/(\d+)$@',
            'matches' => [
                1 => 'page',
            ],
            'admin' => true,
        ],
        'State/Admin/List/TokenRequire' => [
            'regexp' => '@^admin/list/token_require/(\d+)$@',
            'matches' => [
                1 => 'page',
            ],
            'admin' => true,
        ],
        'State/Admin/List/Admins' => [
            'regexp' => '@^admin/list/admins/(\d+)$@',
            'matches' => [
                1 => 'page',
            ],
            'admin' => true,
        ],

        'State/Admin/List/Ban' => [
            'regexp' => '@^admin/list/ban/(\d+)$@',
            'matches' => [
                1 => 'page',
            ],
            'admin' => true,
        ],
        'State/Admin/List/Online' => [
            'regexp' => '@^admin/list/online/(\d+)$@',
            'matches' => [
                1 => 'page',
            ],
            'admin' => true,
        ],
        'State/Admin/List' => [
            'regexp' => '@^admin/list/(\d+)$@',
            'matches' => [
                1 => 'page',
            ],
            'admin' => true,
        ],

        'State/Admin/Default' => [
            'string' => 'admin',
            'admin' => true,
        ],

        'State/Client/General' => [
            'string' => 'general',
        ],

        'State/Client/Penalty/Compensation' => [
            'regexp' => '@^penalty/compensation/(\d+)$@',
            'matches' => [
                1 => 'page',
            ],
        ],

        'State/Client/Penalty/Penalty' => [
            'regexp' => '@^penalty/penalty/(\d+)$@',
            'matches' => [
                1 => 'page',
            ],
        ],

        'State/Client/Penalty/Tasks' => [
            'regexp' => '@^penalty/tasks/(\d+)$@',
            'matches' => [
                1 => 'page',
            ],
        ],

        'State/Client/Penalty/General' => [
            'regexp' => '@^penalty/(\d+)$@',
            'matches' => [
                1 => 'page',
            ],
        ],

        'State/Client/Referrer/How' => [
            'string' => 'referrer/how',
        ],

        'State/Client/Referrer/Balance' => [
            'string' => 'referrer/balance',
        ],

        'State/Client/Referrer/Bonus' => [
            'regexp' => '@^referrer/bonus/(\d+)$@',
            'matches' => [
                1 => 'page',
            ],
        ],

        'State/Client/Referrer/List' => [
            'regexp' => '@^referrer/(\d+)/(\d+)$@',
            'matches' => [
                1 => 'level',
                2 => 'page',
            ],
        ],

        'State/Client/Referrer/Default' => [
            'string' => 'referrer',
        ],

        'State/Client/Bot' => [
            'string' => 'bot',
        ],

        'State/Client/Tasks' => [
            'string' => 'tasks',
        ],

        'State/Client/Karma/List' => [
            'string' => 'karma/list',
        ],

        'State/Client/Karma' => [
            'string' => 'karma',
        ],
        'State/Client/Penalty' => [
            'string' => 'penalty',
        ],
        'State/Client/Bonus' => [
            'string' => 'bonus',
        ],
        'State/Client/Buy' => [
            'string' => 'buy',
        ],
        'State/Client/Services' => [
            'string' => 'services',
        ],

        'State/Client/Me' => [
            'string' => 'me',
        ],
        'State/Client/Register' => [
            'string' => 'register',
        ],
        'State/Client/Login' => [
            'string' => 'login',
        ],
        'State/Client/Exit' => [
            'string' => 'exit',
        ],
        'State/Client/Social/Auth' => [
            'string' => 'social/auth',
        ],
        'State/Client/Social/Callback' => [
            'string' => 'social/callback',
        ],
        'State/Client/Token/Callback' => [
            'string' => 'token/callback',
        ],
    ];
}
