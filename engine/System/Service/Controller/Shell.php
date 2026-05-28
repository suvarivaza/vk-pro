<?php

namespace System;

use Lib_VK;
use Service\Tasks\Model_Tasks_Task;

/**
 * Базовый класс shell-контроллера сервиса
 *
 * @package System
 *
 * @property \Service\Users\Model_Factory $factoryUsers
 * @property \Service\Logs\Model_Logs $log
 * @property \Service\Posting\Model_Factory $factoryPosting
 * @property \Service\Grabber\Model_Factory $factoryGrabber
 * @property \Service\Tasks\Model_Factory $factoryTasks
 * @property \Service\Auto\Model_Factory $factoryAuto
 * @property \Service\Messages\Model_Factory $factoryMessages
 * @property \Service\Bot\Model_Factory $factoryBot
 */
class Service_Controller_Shell implements \System\Service_Controller_Interface
{
    private $_factoryUsers = null;
    private $_factoryLogs = null;
    private $_factoryPosting = null;
    private $_factoryGrabber = null;
    private $_factoryTasks = null;
    private $_factoryAuto = null;
    private $_factoryMessages = null;
    private $_factoryBot = null;
    protected $countStopTasks = 0;

    /**
     * Имя модуля
     *
     * @var string
     */
    public $name = 'base_shell';

    /**
     * Идентификатор процесса
     *
     * @var int
     */
    public $pid = 0;

    /**
     * Параметры контроллера
     *
     * @var array
     */
    protected $params = [
        'debug' => false,
    ];

    /**
     * @var array|null
     */
    protected $settings = null;

    protected $VK = null;

    protected $tokens = ['token', 'token2', 'token3', 'token4', 'token5'];

    public function __construct()
    {
        $this->pid = posix_getpid();
        $this->name = get_class($this);

        //если процесс уже есть завершаем скрипт
        if ($this->isProcessExists()) {
            //print_r('process exist' . PHP_EOL);
            exit;
        }

        $this->settings = $this->getSettings();

        $this->VK = new Lib_VK();
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        $factory = new \Service\System\Model_Factory();
        $it = $factory->settings->getAll();
        $list = [];

        foreach ($it as $setting) {
            $list[$setting->name] = $setting->value;
        }

        return $list;
    }

    /**
     * Действие контроллера
     *
     * @param array $params
     *
     * @return mixed
     */
    final public function Action($params = [])
    {
        $action = $params['__shell_action'];
        unset($params['__shell_action']);
        $this->params = array_replace($this->params, $params);

        if (defined('SHELL_FORCE_DEBUG') && SHELL_FORCE_DEBUG) {
            $this->params['debug'] = true;
        }

        if (method_exists($this, 'A_' . $action)) {
            $this->{'A_' . $action}($params);
        } else {
            $this->A_help();
        }
    }

    /**
     * Возвращает true в случае, если уже запущен процесс с текущей командной строкой
     *
     * @return bool
     */
    public function isProcessExists()
    {
        $cmd = 'ps ax -o pid,command --no-headers|grep "' . ENGINE_PATH . 'shell.php"|grep -v "ps"|grep -v "grep"';
        $processes = shell_exec($cmd);
        $processes = explode("\n", $processes);

        $cmdline = file_get_contents('/proc/' . $this->pid . '/cmdline');
        $cmdline = trim(str_replace(chr(0), ' ', $cmdline));

        foreach ($processes as $p) {
            if (strlen(($p = trim($p))) == 0) {
                continue;
            }
            list($pid, $cl) = explode(' ', $p, 2);

            if ($cmdline == $cl && $this->pid != $pid) {
                return true;
            }
        }

        return false;
    }

    /**
     * Вывод отладочной информации
     *
     * @param $value
     * @param bool $with_newline Добавлять ли PHP_EOL в конце
     */
    protected function log($value = '', $with_newline = true)
    {
        if (false == isset($this->params['debug']) || !$this->params['debug']) {
            return;
        }

        $appendix = '';

        if ($with_newline) {
            $appendix = PHP_EOL;
        }

        if (is_scalar($value)) {
            echo $value, $appendix;
        } else {
            echo var_export($value, true), $appendix;
        }
    }

    /**
     * Отображение списка имеющихся методов
     */
    final public function A_help()
    {
        echo 'Available actions:' . PHP_EOL;

        foreach (get_class_methods(get_class($this)) as $method) {
            if (strncmp('A_', $method, 2) != 0) {
                continue;
            }
            $ref = new \ReflectionMethod(get_class($this), $method);
            $doc = $ref->getDocComment();

            if ($doc) {
                echo preg_replace(["/^\s*\*/m", "/^\s*\//m"], ['   *', '  /'], $doc) . PHP_EOL;
            }
            echo '  ' . substr($method, 2) . PHP_EOL . PHP_EOL;
        }
    }

    public function __get($name)
    {
        switch ($name) {
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
            case 'factoryTasks':
                if (null === $this->_factoryTasks) {
                    $this->_factoryTasks = new \Service\Tasks\Model_Factory();
                }

                return $this->_factoryTasks;
            case 'factoryUsers':
                if (null === $this->_factoryUsers) {
                    $this->_factoryUsers = new \Service\Users\Model_Factory();
                }

                return $this->_factoryUsers;
            case 'factoryAuto':
                if (null === $this->_factoryAuto) {
                    $this->_factoryAuto = new \Service\Auto\Model_Factory();
                }

                return $this->_factoryAuto;
            case 'factoryMessages':
                if (null === $this->_factoryMessages) {
                    $this->_factoryMessages = new \Service\Messages\Model_Factory();
                }

                return $this->_factoryMessages;
            case 'factoryBot':
                if (null === $this->_factoryBot) {
                    $this->_factoryBot = new \Service\Bot\Model_Factory();
                }

                return $this->_factoryBot;
            case 'log':
                if (null === $this->_factoryLogs) {
                    $this->_factoryLogs = new \Service\Logs\Model_Factory();
                }

                return $this->_factoryLogs->getLogs();
        }

        throw new \Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }

    /*
    * Получает один из наших токенов для проверки
    */
    protected function getRandomCheckToken()
    {
        $tokenNumber = rand(0, count($this->tokens) - 1);
        $tokenName = $this->tokens[$tokenNumber];
        return $this->settings[$tokenName];
    }

    /**
     * @param Model_Tasks_Task $task
     * @param $reason
     */
    protected function taskStop(Model_Tasks_Task $task, string $reason)
    {
        $task->makeShadow();
//      $task->isDel = true;
//      $task->isDelDate = time();
        $task->active = false;
        $task->reason = $reason;
        $this->factoryTasks->tasks->save($task);
        $this->countStopTasks++;
    }
}
