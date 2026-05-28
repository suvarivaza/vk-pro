<?php

namespace Service\Tasks;

class Controller_Router extends \System\Service_Controller_Router_Rewrite
{
    protected $routes = [
        'State_Admin_List_Done' => [
            'string' => 'admin/list/done',
            'admin' => true,
        ],
        'State_Admin_List_Del' => [
            'string' => 'admin/list/del',
            'admin' => true,
        ],
        'State_Admin_List_Active' => [
            'string' => 'admin/list/active',
            'admin' => true,
        ],
        'State_Admin_Stat_Default' => [
            'string' => 'admin/stat',
            'admin' => true,
        ],
        'State_Admin_Countries' => [
            'string' => 'admin/countries',
            'admin' => true,
        ],
        'State_Admin_Cities' => [
            'string' => 'admin/cities',
            'admin' => true,
        ],

        'State_Admin_Default_Special' => [
            'string' => 'admin/list/special',
            'admin' => true,
        ],

        'State_Admin_Special_Default' => [
            'string' => 'admin/special',
            'admin' => true,
        ],

        'State_Admin_Abuse' => [
            'string' => 'admin/abuse',
            'admin' => true,
        ],
        'State_Admin_Tips_Menu' => [
            'string' => 'admin/tips/menu',
            'admin' => true,
        ],

        'State_Admin_Tips_Special_Fields' => [
            'string' => 'admin/tips/special/fields',
            'admin' => true,
        ],

        'State_Admin_Tips_Fields' => [
            'string' => 'admin/tips/fields',
            'admin' => true,
        ],

        'State_Admin_Tips_Messages' => [
            'regexp' => '@^admin/messages$@',
            'matches' => [
                1 => 'type',
            ],
            'admin' => true,
        ],

        'State_Admin_Tips_Special_Tasks' => [
            'regexp' => '@^admin/tips/special/(likes|reposts|comments|join|friends|polls|views|video|token)$@',
            'matches' => [
                1 => 'type',
            ],
            'admin' => true,
        ],

        'State_Admin_Tips_Tasks' => [
            'regexp' => '@^admin/tips/(likes|reposts|comments|join|friends|polls|views|video|token)$@',
            'matches' => [
                1 => 'type',
            ],
            'admin' => true,
        ],

        'State_Admin_Percents' => [
            'string' => 'admin/prices/percents',
            'admin' => true,
        ],
        'State_Admin_Prices' => [
            'string' => 'admin/prices',
            'admin' => true,
        ],
        'State_Admin_Blacklist' => [
            'string' => 'admin/blacklist',
            'admin' => true,
        ],
        'State_Admin_Limits_User' => [
            'string' => 'admin/limits/user',
            'admin' => true,
        ],
        'State_Admin_Limits' => [
            'string' => 'admin/limits',
            'admin' => true,
        ],
        'State_Admin_Default' => [
            'string' => 'admin',
            'admin' => true,
        ],

        'State_Client_Special_Join' => [
            'regexp' => '@^special/join/(\d+)$@',
            'matches' => [
                1 => 'specialId',
            ],
        ],

        'State_Client_Special_Add' => [
            'regexp' => '@^special/(\d+)/add$@',
            'matches' => [
                1 => 'specialId',
            ],
        ],
        'State_Client_Special_List' => [
            'regexp' => '@^special/(\d+)/(\w+)/(\d+)$@',
            'matches' => [
                1 => 'groupId',
                2 => 'type',
                3 => 'page',
            ],
        ],

        'State_Client_Special_Buy' => [
            'string' => 'special/buy',
        ],

        'State_Client_Special_Default' => [
            'string' => 'special',
        ],
        'State_Client_Edit' => [
            'regexp' => '@^my/edit/(\d+)$@',
            'matches' => [
                1 => 'taskId',
            ],
        ],
        'State_Client_Add' => [
            'string' => 'add',
        ],
        'State_Client_My' => [
            'regexp' => '@^my/(\w+)/(\d+)$@',
            'matches' => [
                1 => 'type',
                2 => 'page',
            ],
        ],
        'State_Client_Go' => [
            'string' => 'go',
        ],
        'State_Client_ListNew' => [
            'regexp' => '@^(\w+)/new$@',
            'matches' => [
                1 => 'type',
            ],
        ],
        'State_Client_List_Bonus' => [
            'string' => 'bonus',
        ],
        'State_Client_List' => [
            'regexp' => '@^(\w+)$@',
            'matches' => [
                1 => 'type',
            ],
        ],
        'State_Client_Default' => [
            'string' => '',
        ],
    ];
}
