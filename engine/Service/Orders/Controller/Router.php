<?php

namespace Service\Orders;

class Controller_Router extends \System\Service_Controller_Router_Rewrite
{
    protected $routes = [
        'State_Client_Yandex_Success' => [
            'string' => 'success',
        ],
        'State_Client_Yandex_Success_Test' => [
            'string' => 'success_test',
        ],

        'State_Client_Yandex_Error' => [
            'string' => 'error',
        ],
        'State_Client_Yandex_Error_Test' => [
            'string' => 'error_test',
        ],

        'State_Client_SpryPay_Ipn' => [
            'string' => 'sprypay/ipn',
        ],
        'State_Client_Yandex_Aviso' => [
            'string' => 'yandex/aviso',
        ],
        'State_Client_Yandex_Aviso_Test' => [
            'string' => 'yandex/aviso_test',
        ],

        'State_Client_Yandex_Check' => [
            'string' => 'yandex/check',
        ],
        'State_Client_Yandex_Check_Test' => [
            'string' => 'yandex/check_test',
        ],

        'State_Admin_List' => [
            'regexp' => '@^admin/list/(\d+)$@',
            'matches' => [
                1 => 'page',
            ],
            'admin' => true,
        ],

        'State_Admin_Pack_Edit' => [
            'regexp' => '@^admin/pack/edit/(\d+)$@',
            'matches' => [
                1 => 'packId',
            ],
            'admin' => true,
        ],
        'State_Admin_Pack_Add' => [
            'string' => 'admin/pack/add',
            'admin' => true,
        ],
        'State_Admin_Services' => [
            'string' => 'admin/services',
            'admin' => true,
        ],
        'State_Admin_Stat' => [
            'string' => 'admin/stat',
            'admin' => true,
        ],
        'State_Admin_Settings' => [
            'string' => 'admin/settings',
            'admin' => true,
        ],
        'State_Admin_Default' => [
            'string' => 'admin',
            'admin' => true,
        ],

        'State_Client_Action_Ref' => [
            'string' => 'action/ref',
        ],

        'State_Client_Done' => [
            'string' => 'done',
        ],

        'State_Client_Order' => [
            'string' => 'order',
        ],

        'State_Client_Buy' => [
            'string' => 'buy',
        ],

        'State_Client_Default' => [
            'string' => '',
        ],

        'State_Client_YooKassa_Pay' => [
            'string' => 'pay',
        ],

        'State_Client_YooKassa_PayResult' => [
            'string' => 'pay_result',
        ],

    ];
}
