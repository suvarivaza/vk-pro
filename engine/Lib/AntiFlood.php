<?php

/**
 * Блокировка флада
 */
class Lib_AntiFlood
{
    /**
     * константы для генерации ключа, этим занимается сама библиотека
     */
    public const K_IP = 0x0001; // ip адрес компа и прокси
    public const K_USER = 0x0002; // идентификатор пользователя
    public const K_CUID = 0x0004; // uid из cookie пользователя от nginx, разные по сайтам

    /**
     * @var Memcache
     */
    private $_redis = null;
    private $_env = [];

    /**
     * @param string $ip
     * @param int $userID
     * @param string $requestUID
     */
    public function __construct($ip, $userID, $requestUID)
    {
        $this->_redis = new \Memcache();
        $this->_redis->pconnect('localhost', 11211);

        $this->_env[self::K_IP] = $ip;
        $this->_env[self::K_USER] = $userID;
        $this->_env[self::K_CUID] = $requestUID;
    }

    /**
     * Возвращает состояние блокировки с зачислением запроса.
     *
     * @param string $action наименование действия
     * @param int $key_template шаблон типа ключа: или побитовая сумма  констант K_IP, K_USER, K_CUID
     * @param int $ttl период времени
     * @param int $request_count кол-во запросов за период
     *
     * @throws Lib_Exception_InvalidArgument
     *
     * @return bool
     */
    public function Score($action, $key_template, $ttl, $request_count = 1)
    {
        if (!is_int($ttl) || !is_int($request_count) || $ttl <= 0 || $request_count <= 0) {
            throw new Lib_Exception_InvalidArgument('Invalid arguments supplied for function Antiflood::Score()');
        }

        try {
            $action_key = $this->GetKey($action, $key_template);

            $result = true;

            // устанавливается счетчик запросов
            if (!$this->_redis->get($action_key)) {
                $this->_redis->set($action_key, $request_count - 1, false, $ttl);
            } else {
                $value = $this->_redis->get($action_key);

                if ($value <= 0) {
                    $result = false;
                }

                $current_ttl = $this->_redis->TTL($action_key);

                if ($current_ttl <= 0) {
                    $current_ttl = $ttl;
                }

                $this->_redis->Set(
                    $action_key,
                    $value - 1,
                    $current_ttl
                );
            }

            return $result;
        } catch (\Exception $e) {
            if (!($e instanceof Lib_Exception_Backtrace_Interface)) {
                Lib_Trace::BacktraceException($e);
            }
        }

        return true;
    }

    /**
     * Возвращает состояние блокировки
     *
     * @static
     *
     * @param string $action наименование действия
     * @param int $key_template шаблон типа ключа: или побитовая сумма  констант K_IP, K_USER, K_CUID
     *
     * @return bool
     */
    public function Check($action, $key_template)
    {
        try {
            $action_key = $this->GetKey($action, $key_template);

            // устанавливается счетчик запросов
            if (!$this->_redis->Exists($action_key)) {
                return true;
            }

            $value = $this->_redis->Get($action_key);

            if ($value <= 0) {
                return false;
            }
        } catch (\Exception $e) {
            if (!($e instanceof Lib_Exception_Backtrace_Interface)) {
                Lib_Trace::BacktraceException($e);
            }
        }

        return true;
    }

    /**
     * Сбрасывает состояние блокировки
     *
     * @static
     *
     * @param string $action
     * @param int $key_template Шаблон типа ключа: или побитовая сумма  констант K_IP, K_USER, K_CUID
     */
    public function Reset($action, $key_template)
    {
        try {
            $action_key = $this->GetKey($action, $key_template);
            $this->_redis->Del($action_key);
        } catch (\Exception $e) {
            if (!($e instanceof Lib_Exception_Backtrace_Interface)) {
                Lib_Trace::BacktraceException($e);
            }
        }
    }

    /**
     * @param string $action
     * @param int $key_template
     *
     * @return string
     *
     * @throws Lib_Exception_InvalidArgument
     */
    public function GetKey($action, $key_template)
    {
        if (!is_string($action) || !is_int($key_template) || empty($action) || empty($key_template)) {
            throw new Lib_Exception_InvalidArgument('Invalid arguments supplied for function Antiflood::GetKey()');
        }
        $k[] = $action;

        if ($key_template & self::K_IP) {
            $k[] = 'ip=' . $this->_env[$key_template & self::K_IP];
        }

        if ($key_template & self::K_USER) {
            $k[] = 'user=' . $this->_env[$key_template & self::K_USER];
        }

        if ($key_template & self::K_CUID) {
            $k[] = 'cuid=' . $this->_env[$key_template & self::K_CUID];
        }

        return implode($k, ':');
    }
}
