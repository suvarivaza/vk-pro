<?php

namespace System;

use Lib_VK;

/**
 * Базовый класс контроллера состояния сервиса
 *
 * @package System
 *
 * @property \Service\Users\Model_Factory $factoryUsers
 * @property \Service\Pages\Model_Pages $factoryPages
 * @property \Service\News\Model_Factory $factoryNews
 * @property \Service\Messages\Model_Factory $factoryMessages
 * @property \Service\Tasks\Model_Factory $factoryTasks
 * @property \Service\System\Model_Factory $factorySystem
 * @property \Service\Faq\Model_Factory $factoryFaq
 * @property \Service\Posting\Model_Factory $factoryPosting
 * @property \Service\Grabber\Model_Factory $factoryGrabber
 * @property \Service\Auto\Model_Factory $factoryAuto
 * @property \Service\Orders\Model_Factory $factoryOrders
 * @property \Service\Bot\Model_Factory $factoryBot
 */
abstract class Service_Controller_State implements \System\Service_Controller_Interface
{
    /**
     * Имя сервиса
     *
     * @var string
     */
    protected $_service = '';

    /**
     * Параметры
     *
     * @var array
     */
    protected $_params = [];

    /**
     * HTTP ответ
     *
     * @var HttpResponse
     */
    protected $_response;

    /**
     * HTTP запрос
     *
     * @var HttpRequest
     */
    protected $_request;

    /**
     * @var \System\App
     */
    protected $_application;

    protected $_errors = null;

    /** @var \Service\Users\Model_Factory */
    private $_factoryUsers = null;

    /** @var \Service\Pages\Model_Pages */
    private $_factoryPages = null;

    /** @var \Service\News\Model_Factory */
    private $_factoryNews = null;
    /** @var \Service\Tasks\Model_Factory */
    private $_factoryTasks = null;
    /** @var \Service\System\Model_Factory */
    private $_factorySystem = null;
    private $_factoryMessages = null;
    private $_factoryFaq = null;
    private $_factoryPosting = null;
    private $_factoryGrabber = null;
    private $_factoryAuto = null;
    private $_factoryOrders = null;
    private $_factoryBot = null;

    protected $VK = null;

//    protected $_tokens = ['token', 'token2', 'token3', 'token4', 'token5'];

    protected $user_access_token = null;
    protected $service_access_token = null;
    protected $check_access_token = null;

    /**
     * Конструктор
     */
    public function __construct(\System\App $app)
    {

        $this->_application = $app;

        if($this->_application->User) Lib_VK::init($this->_application->User);
        $this->VK = new Lib_VK();

        $this->check_access_token = $this->_application->check_access_token;
        $this->service_access_token = $this->_application->settings['service'];
        $this->user_access_token = $this->_application->User->access_token;

        $className = get_class($this);
        list(, $service) = explode('\\', $className);

        if (!empty($service)) {
            $this->_service = $service;
        }

        //устанавливаем папку шаблона для шаблонизатора
        \STPL::PathRegister(ENGINE_PATH . 'engine/Service/' . $this->_service . '/Template/');

        $this->_request = new \System\HttpRequest('', $_GET, $_POST, $_COOKIE, $_SERVER);
        $this->_response = new \System\HttpResponse();
    }

    /**
     * Действие контроллера
     *
     * @param $params
     *
     * @return HttpResponse|void|null
     */
    public function Action($params = null)
    {

        $this->_params = $params;

        $response = $this->actionPrepare();

        if ($response instanceof \System\HttpResponse) {
            return $response;
        }

        switch ($this->_request->getMethod()) {
            case HttpRequest::HEAD:
                $response = $this->actionHead();
                break;

            case HttpRequest::POST:
                $response = $this->actionPost();
                break;

            case HttpRequest::PUT:
                $response = $this->actionPut();
                break;

            case HttpRequest::DELETE:
                $response = $this->actionDelete();
                break;

            case HttpRequest::OPTIONS:
                $response = $this->actionOptions();
                break;

            case HttpRequest::GET:
                break;

            default:
                return \System\HttpResponse::Status(501);
        }


        if ($response instanceof \System\HttpResponse) {
            return $response;
        }


        return $this->actionGet();
    }

    /**
     * Обработчик инициализации (вызывается до всех действий).
     *
     * @return \System\HttpResponse|null
     */
    public function actionPrepare()
    {
        return null;
    }

    /**
     * @return \System\HttpResponse|null
     */
    abstract public function actionGet();

    /**
     * @return \System\HttpResponse|null
     */
    abstract public function actionPost();

    /**
     * @return \System\HttpResponse|null
     */
    public function actionHead()
    {
    }

    /**
     * @return \System\HttpResponse|null
     */
    public function actionPut()
    {
    }

    /**
     * @return \System\HttpResponse|null
     */
    public function actionDelete()
    {
    }

    /**
     * @return \System\HttpResponse|null
     */
    public function actionOptions()
    {
    }

    /**
     * @param $name
     *
     * @return \Service\News\Model_Factory|\Service\Pages\Model_Pages|\Service\Users\Model_Factory|\Service\Tasks\Model_Factory|\Service\System\Model_Factory|\Service\Messages\Model_Factory
     *
     * @throws \Lib_Exception_UnknownProperty_Backtraced
     */
    public function __get($name)
    {

        switch ($name) {
            case 'factoryUsers':
                if (null === $this->_factoryUsers) {
                    $this->_factoryUsers = new \Service\Users\Model_Factory();
                }

                return $this->_factoryUsers;
            case 'factoryNews':
                if (null === $this->_factoryNews) {
                    $this->_factoryNews = new \Service\News\Model_Factory();
                }

                return $this->_factoryNews;
            case 'factoryPages':
                if (null === $this->_factoryPages) {
                    $this->_factoryPages = new \Service\Pages\Model_Pages();
                }

                return $this->_factoryPages;
            case 'factoryTasks':
                if (null === $this->_factoryTasks) {
                    $this->_factoryTasks = new \Service\Tasks\Model_Factory();
                }

                return $this->_factoryTasks;
            case 'factorySystem':
                if (null === $this->_factorySystem) {
                    $this->_factorySystem = new \Service\System\Model_Factory();
                }

                return $this->_factorySystem;
            case 'factoryMessages':
                if (null === $this->_factoryMessages) {
                    $this->_factoryMessages = new \Service\Messages\Model_Factory();
                }

                return $this->_factoryMessages;
            case 'factoryFaq':
                if (null === $this->_factoryFaq) {
                    $this->_factoryFaq = new \Service\Faq\Model_Factory();
                }

                return $this->_factoryFaq;
            case 'factoryPosting':
                if (null === $this->_factoryPosting) {
                    $this->_factoryPosting = new \Service\Posting\Model_Factory();
                }

                return $this->_factoryPosting;
            case 'factoryGrabber':
                if (null === $this->_factoryGrabber) {
                    $this->_factoryGrabber = new \Service\Grabber\Model_Factory();
                }

                return $this->_factoryGrabber;
            case 'factoryAuto':
                if (null === $this->_factoryAuto) {
                    $this->_factoryAuto = new \Service\Auto\Model_Factory();
                }

                return $this->_factoryAuto;
            case 'factoryOrders':
                if (null === $this->_factoryOrders) {
                    $this->_factoryOrders = new \Service\Orders\Model_Factory();
                }

                return $this->_factoryOrders;
            case 'factoryBot':
                if (null === $this->_factoryBot) {
                    $this->_factoryBot = new \Service\Bot\Model_Factory();
                }

                return $this->_factoryBot;
        }

        throw new \Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }

    public function fetchMail($vars)
    {
        \STPL::PathRegister(ENGINE_PATH . 'engine/Service/System/Template/');

        return \STPL::Fetch('mail', $vars);
    }

    protected function my_ucfirst($string, $e = 'utf-8')
    {
        if (function_exists('mb_strtoupper') && function_exists('mb_substr') && !empty($string)) {
            $string = mb_strtolower($string, $e);
            $upper = mb_strtoupper($string, $e);
            preg_match('#(.)#us', $upper, $matches);
            $string = $matches[1] . mb_substr($string, 1, mb_strlen($string, $e), $e);
        } else {
            $string = ucfirst($string);
        }

        return $string;
    }

}
