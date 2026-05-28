<?php

namespace Service\Admin;

class Controller_Router extends \System\Service_Controller_Router_Rewrite
{
    protected $routes = [
        'State/Start' => [
            'string' => 'start',
        ],
        'State/Default' => [
            'regexp' => '@.*@',
            'matches' => [
                0 => 'path',
            ],
        ],
    ];
}
