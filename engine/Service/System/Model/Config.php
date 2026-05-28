<?php

namespace Service\System;

class Model_Config
{
    public static $menu = [
        'main' => [
            'title' => 'Главная',
            'href' => '/',
            'active' => false,
        ],
        'about' => [
            'title' => 'О компании',
            'href' => '/about',
            'active' => false,
        ],
        'shipment' => [
            'title' => 'Отгрузка',
            'href' => '/shipment',
            'active' => false,
        ],
        'contacts' => [
            'title' => 'Контакты',
            'href' => '/contacts',
            'active' => false,
        ],
    ];
}
