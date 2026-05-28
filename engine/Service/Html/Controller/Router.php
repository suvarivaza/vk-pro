<?php

namespace Service\Html;

class Controller_Router extends \System\Service_Controller_Router_Rewrite
{
    protected $routes = [
        'Default' => [
            'regexp' => '@(.*)@',
            'matches' => [
                '1' => 'path',
            ],
        ],
    ];
}
