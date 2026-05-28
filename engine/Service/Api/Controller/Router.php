<?php

namespace Service\Api;

use System\Service_Controller_Router_Rewrite;

class Controller_Router extends Service_Controller_Router_Rewrite
{
    protected $routes = [
        'State_Client_Balance' => [
            'string' => 'balance',
        ],
        'State_Client_Karma' => [
            'string' => 'karma',
        ],
        'State_Client_Login' => [
            'string' => 'login',
        ],
        'State_Client_Register' => [
            'string' => 'register',
        ],
        'State_Client_Tasks' => [
            'string' => 'tasks',
        ],
        'State_Client_User' => [
            'string' => 'user',
        ],
        'State_Client_Bot' => [
            'string' => 'bot',
        ],

        'State_Client_Sync' => [
            'string' => 'sync/projects'
        ],

        'State_Client_Default' => [
            'string' => '',
        ],
    ];
}
