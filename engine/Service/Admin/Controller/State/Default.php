<?php

namespace Service\Admin;

/**
 * Class Controller_State_Default
 *
 * @package Service\Admin
 */
class Controller_State_Default extends \System\Service_Controller_State
{
    public function actionPrepare()
    {
        if (!$this->_application->UserIsAuth() || !in_array($this->_application->User->userType,
                [\Service\Users\Model_Config::TYPE_ADMIN, \Service\Users\Model_Config::TYPE_MODERATOR])) {
            return $this->_response->setStatus(\System\HttpResponse::S3_FOUND)->setHeader('Location', '/users/login');
        }

        $this->_application->Title->addStyles([
                '/css/bootstrap.min.css',
                '/css/bower_components/font-awesome/css/font-awesome.min.css',
                '/css/bower_components/Ionicons/css/ionicons.min.css',
                '/css/AdminLTE.min.css',
                '/css/skins/_all-skins.min.css',
                '/css/bower_components/morris.js/morris.css',
                '/css/bower_components/jvectormap/jquery-jvectormap.css',
                '/css/styles.min.css',
                '/css/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css',
            ]
        );

        $this->_application->Title->addScripts([
            '/css/bower_components/jquery/dist/jquery.min.js',
            '/css/bower_components/jquery-ui/jquery-ui.min.js',
            '/css/bower_components/bootstrap/dist/js/bootstrap.min.js',
            '/css/bower_components/raphael/raphael.min.js',
            '/css/bower_components/morris.js/morris.min.js',
            '/css/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js',
            '/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js',
            '/plugins/jvectormap/jquery-jvectormap-world-mill-en.js',
            '/css/bower_components/jquery-knob/dist/jquery.knob.min.js',
            '/css/bower_components/moment/min/moment.min.js',
            '/css/bower_components/bootstrap-daterangepicker/daterangepicker.js',
            '/css/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
            '/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js',
            '/css/bower_components/jquery-slimscroll/jquery.slimscroll.min.js',
            '/css/bower_components/fastclick/lib/fastclick.js',
            '/js/floating-labels.min.js',
            '/js/adminlte.min.js',
            '/js/pages/dashboard.js',
            '/js/demo.js',
            '/js/dialog.min.js',
        ]);

        $this->_application->admin = true;
        $this->_application->menu = Model_Config::$menu;

        $path = $this->_params['path'];

        list($service) = explode('/', $path);

        if (!$service) {
            return $this->actionGet();
        }

        $routing_path = substr($path, strlen($service));
        $routing_path = 'admin' . $routing_path;

        $service = \Config::$services[$service];

        if ($service == 'Start') {
            $controller = \System\Service_Controller_Factory::getInstance('Admin', 'State_Start', $this->_application);

            return $controller->Action($this->_params);
        }

        $class = '\\Service\\' . $service . '\\Controller_Router';

        if (!class_exists($class)) {
            $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);

            return $this->_response;
        }

        /** @var \System\Service_Controller_Router_Rewrite $router */
        $router = new $class($this->_application);
        $router->setAdmin();

        $response = $router->Action(['__routing_path' => $routing_path]);

        if ($response->getContentType() == 'application/json') {
            return $response;
        }

        if ($response->getStatus() != \System\HttpResponse::S2_OK) {
            return $response;
        }

        $this->_response = clone $response;
        $vars = [
            'app' => $this->_application,
            'user' => $this->_application->User,
            'html' => $response->getBody(),
        ];


        $query = $this->factoryFaq->questions->query()->limit(5)->sqlCalcFoundRows(true)->sort('qId', 'ASC');
        $query->filter->fieldValue('isNewQuestion', '=', true);
        $it = $query->iterator();
        $total = $it->getTotal();
        $list = [];

        foreach ($it as $q) {
            $list[] = $q;
        }

        $vars['questions'] = [
            'total' => $total,
            'list' => $list,
        ];

        echo $this->_response->setBody(\STPL::Fetch('default_new', $vars));
        exit;
    }

    /**
     * @return \System\HttpResponse|null
     */
    public function actionGet()
    {
        $this->_application->menu['general']['active'] = true;
        $query = $this->factoryFaq->questions->query()->limit(5)->sqlCalcFoundRows(true)->sort('qId', 'ASC');
        $query->filter->fieldValue('isNewQuestion', '=', true);
        $it = $query->iterator();
        $total = $it->getTotal();
        $list = [];

        foreach ($it as $q) {
            $list[] = $q;
        }

        $usersTotal = $this->factoryUsers->users->getCountTotal();
        $tasksTotal = $this->factoryTasks->tasks->getCountTotal();
        $specialTotal = $this->factoryTasks->tasks->getCountTotal(true);
        $ordersTotal = $this->factoryOrders->orders->getCountTotal();

        $now = time();
        $from = strtotime('-6 MONTH');
        $from = strtotime('FIRST DAY', $from);

        //$countsUsers = $this->factoryUsers->users->getCountsByMonth($from);
        $countsOrders = $this->factoryOrders->orders->getCountsByMonth($from);

        $date = $from;
        $countsOrders = [
            'keys' => [
                'count',
                'sum',
            ],
            'labels' => [
                'Количество',
                'Сумма',
            ],
            'values' => [],
        ];

        while ($date < $now) {
            $key = \Lib_TimeStamp::createFromTimestamp($date)->format('Y-m');
            $month = date('m', $date);

            $countsOrders['values'][$key] = [
                'count' => $countsOrders[$month]['count'] ?? 0,
                'sum' => $countsOrders[$month]['sum'] ?? 0,
            ];

            $date = strtotime('+1 MONTH', $date);
        }

        $vars = [
            'totals' => [
                'users' => $usersTotal,
                'tasks' => $tasksTotal,
                'special' => $specialTotal,
                'orders' => $ordersTotal,
            ],
            'countsOrders' => $countsOrders,
            'app' => $this->_application,
            'questions' => [
                'total' => $total,
                'list' => $list,
            ],
        ];
        echo $this->_response->setBody(\STPL::Fetch('new/index', $vars));

        exit;
    }

    /**
     * @return \System\HttpResponse|null
     */
    public function actionPost()
    {
        return null;
    }
}
