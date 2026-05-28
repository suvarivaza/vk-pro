<?php

namespace Service\Bot;

use System\Service_Controller_Router_Rewrite;

class Controller_Router extends Service_Controller_Router_Rewrite
{
    protected $routes = [
        'State_Admin_Default' => [
            'string' => 'admin',
            'admin' => true,
        ],
        'State_Client_Default' => [
            'regexp' => '@(.*)@',
            'matches' => [
                '1' => 'path',
            ],
        ],
    ];
}
