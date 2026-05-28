<?php

/**
 * Подключение к БД
 *
 * @throws Lib_Exception_Runtime_Backtraced
 *
 * @property string $insert_id
 * @property string $errno
 * @property string $error
 * @property int $affected_rows
 * @property string $name
 * @property string $host
 * @property string $host_ip
 * @property int $port
 * @property string $user
 * @property string $pass
 * @property Lib_DB_Adapter $slave
 * @property Lib_DB_Adapter $slave2
 * @property Lib_DB_Adapter $backup
 * @property Lib_DB_Adapter $rpc
 */
class Lib_DB_Adapter
{
    /** количество строк, при update которого надо орать в лог */
    public const MAX_CHANGED_ROWS = 1000;
    /** драйвер БД по умолчанию */
    public const DEFAULT_DRIVER = 'mysql';
    /** выполнять ли пинг сервера перед каждым запросом  */
    public const PING_BEFORE_QUERY = false;
    /** количество попыток переподключения из-за "ушедшего" сервера */
    public const NATTMPTS_ON_GONE_AWAY = 1;

    /**
     * Файл логов
     *
     * @var string
     */
    public static $log_file = '/tmp/db_query.log123';

    /**
     * Объект PDO
     *
     * @var PDO
     */
    private $_object = null;

    /**
     * Хост
     *
     * @var string
     */
    private $_host = null;

    /**
     * IP сервера БД
     *
     * @var string
     */
    private $_host_ip = null;

    /**
     * Порт сервера БД
     *
     * @var string
     */
    private $_port = null;

    /**
     * Драйвер БД
     *
     * @var string
     */
    private $_driver = '';

    /**
     * Опции драйвера
     *
     * @var array
     */
    private $_driver_options = [];

    /**
     * Информация о подключении
     *
     * @var array|null
     */
    private $_info = null;

    /**
     * Атрибуты подключения
     *
     * @var array
     */
    private $_attributes = [];

    /**
     * Имя пользователя
     *
     * @var string
     */
    private $_username = null;

    /**
     * Пароль
     *
     * @var string
     */
    private $_passwd = null;

    /**
     * Имя БД
     *
     * @var string
     */
    private $_dbname = null;

    /**
     * DSN подключения
     *
     * @var string
     */
    private $_dsn = '';

    /**
     * Флаг подключения
     *
     * @var bool
     */
    private $_is_connected = false;

    /**
     * Время последнего подключения
     *
     * @var int
     */
    private $_last_connected = null;

    /**
     * Количество строк, измененных последним запросом
     *
     * @var int
     */
    private $_affected_rows = 0;

    private $_longWait;

    /**
     * Использовать профайлинг
     *
     * @var bool
     */
    private static $_profiling = false;

    /**
     * в случае использования профайлинга хранит все конекты, чтобы потом показать
     *
     * @var array
     */
    private static $_connections = [];

    /**
     * Конструктор
     *
     * @param string $host Хост подключения
     * @param string $username Пользователь
     * @param string $password Пароль
     * @param string $dbname Имя базы
     * @param array $info Информиция о подключении (для мастера)
     * @param null $port Порт
     * @param string $driver Драйвер
     * @param bool $longWait Увеличенное время простоя
     *
     * @throws Lib_Exception_Runtime_Backtraced
     */
    public function __construct($host, $username, $password, $dbname, $info = null, $port = null, $driver = self::DEFAULT_DRIVER, $longWait = false)
    {
        if (empty($driver)) {
            throw new Lib_Exception_Runtime_Backtraced('Invalid database driver');
        }

        if (false === in_array($driver, PDO::getAvailableDrivers())) {
            throw new Lib_Exception_Runtime_Backtraced("Unavailable PDO driver '" . $driver . "'");
        }
        $this->_info = $info;

        $this->_host = $host;

        if (preg_match('/\d+\.\d+\.\d+\.\d+/', $this->_host)) {
            $this->_host_ip = $this->_host;
        } else {
            $this->_host_ip = gethostbyname($this->_host);

            if ($this->_host_ip == $this->_host) {
                throw new Lib_Exception_Runtime_Backtraced("WTF??? '" . $this->_host_ip . "' must be IP, but not hostname.");
            }
        }

        $this->_username = $username;
        $this->_passwd = $password;
        $this->_dbname = $dbname;
        $this->_driver = $driver;
        $this->_port = $port;
        $this->_dsn = self::__createDSN($this->_driver, $this->_dbname, $this->_host, $this->_port);
        $this->_longWait = (bool) $longWait;

        if ($info !== null) {
            if (self::$_profiling === true) {
                self::$_connections[] = $this;
            }
        }
    }

    /**
     * Деструктор
     */
    public function __destruct()
    {
        if ($this->_object !== null) {
            unset($this->_object);
            $this->_object = null;
        }
    }

    /**
     * Создает DSN по заданному драйверу, хосту и имени БД
     *
     * @static
     *
     * @param string $driver Драйвер
     * @param string $dbname Имя БД
     * @param string $host Хост
     * @param int|null $port Порт
     *
     * @return string
     */
    private static function __createDSN($driver, $dbname, $host, $port = null)
    {
        $dsn = $driver . ':dbname=' . $dbname . ';host=' . $host;

        if (null !== $port) {
            $dsn .= ';port=' . $port;
        }

        return $dsn;
    }

    /**
     * Фактическое подключение к БД
     *
     * @throws Lib_Exception_Runtime_Backtraced
     * @throws Lib_Exception_Runtime_Backtraced
     */
    private function __connect()
    {
        if ($this->_object !== null) {
            return;
        }

        $connectError = '';

        try {
            $this->_object = new PDO(
                $this->_dsn,
                $this->_username,
                $this->_passwd,
                $this->_driver_options
            );
            $this->_is_connected = true;
        } catch (PDOException $e) {
            $this->_is_connected = false;
            $connectError = $e->getCode() . ': ' . $e->getMessage();
        }

        if ($this->_is_connected === false) {
            throw new Lib_Exception_Runtime_Backtraced($this->_dsn . "Can't connect to " . $this->_driver . ' server with database name ' . $this->_dbname . ' on ' . $this->_host_ip . ' with message: "' . $connectError . '"');
        }
        $this->_last_connected = microtime(true);

        if ($this->_longWait) {
            $this->_object->query('SET wait_timeout=86400');
        } else {
            // нафиг \App из либы
            $this->_object->query('SET wait_timeout=10');
        }

        $this->_object->query('SET NAMES UTF8');
        $this->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        $this->__restoreAttributes();

        if (self::$_profiling === true) {
            $this->_object->query('set profiling=1');
        }
    }

    /**
     * Фактическое отключение от БД
     *
     * @return bool
     */
    private function __disconnect()
    {
        if ($this->_object !== null) {
            unset($this->_object);
            $this->_object = null;
        }

        return true;
    }

    /**
     * Восстановление атрибутов для переподключения
     *
     * @return mixed
     *
     * @throws Lib_Exception_Runtime_Backtraced
     */
    private function __restoreAttributes()
    {
        if ($this->_object === null) {
            return;
        }

        foreach ($this->_attributes as $attribute => $value) {
            if (false === $this->_object->setAttribute($attribute, $value)) {
                throw new Lib_Exception_Runtime_Backtraced('Restore attribute error');
            }
        }
    }

    /**
     * Фозвращает флаг подключения
     *
     * @return bool
     */
    public function isConnected()
    {
        return $this->_is_connected;
    }

    /**
     * Установить плаг профайлинга
     *
     * @static
     *
     * @param $enabled
     */
    public static function SetProfiling($enabled)
    {
        if (is_bool($enabled)) {
            self::$_profiling = $enabled;
        }
    }

    /**
     * Переключится на указанный хост
     * Отключается от предыдущего хоста и устанавливает новое имя и IP
     * Но не подключается, т.к. подключение ленивое
     *
     * @param string $host имя или адрес хоста
     * @param int|null $port порт
     *
     * @return string
     */
    public function setHost($host, $port = null)
    {
        $this->_host = $host;
        $this->_host_ip = gethostbyname($this->_host);
        $this->_port = $port;
        $this->_dsn = self::__createDSN($this->_driver, $this->_dbname, $this->_host_ip, $this->_port);
        $this->close();

        return true;
    }

    /**
     * Установка опции драйвера
     *
     * @param string $name Имя
     * @param string $value Значение
     */
    public function setOption($name, $value)
    {
        $this->_driver_options[$name] = $value;
    }

    /**
     * Установка опций драйвера
     *
     * @param $driver_options
     */
    public function setOptions($driver_options)
    {
        $this->_driver_options = $driver_options;
    }

    /**
     * Установка атрибута
     *
     * @param $attribute
     * @param $value
     * @param bool $setOnSlaves [optional] - прокидывать ли атрибут слейвам
     *
     * @throws Lib_Exception_Runtime_Backtraced
     */
    public function setAttribute($attribute, $value)
    {
        $this->_attributes[$attribute] = $value;

        if ($this->_object !== null) {
            if (false === $this->_object->setAttribute($attribute, $value)) {
                throw new Lib_Exception_Runtime_Backtraced('Set attribute error');
            }
        }
    }

    /**
     * Получить флаг профайлинга
     *
     * @static
     *
     * @return bool
     */
    public static function getProfiling()
    {
        return self::$_profiling;
    }

    /**
     * @static
     *
     * @return array
     */
    public static function getProfiles()
    {
        $profiles = [];
        /**
         * @var Lib_DB_Adapter
         */
        foreach (self::$_connections as $c) {
            $res = $c->query('SHOW PROFILES');

            if ($res === false) {
                return $profiles;
            }

            $profile = [
                'title' => 'Host: ' . $c->host_ip . ' DB: ' . $c->name,
                'profile' => [],
            ];

            while ($row = $res->fetch_assoc()) {
                $profile['profile'][] = [
                    'id' => $row['Query_ID'],
                    'time' => $row['Duration'],
                    'query' => $row['Query'],
                ];
            }

            $profiles[] = $profile;

            if ($c->slave !== null) {
                $res = $c->slave->query('SHOW PROFILES');
                $profile = [
                    'title' => 'Host: ' . $c->slave->host_ip . ' DB: ' . $c->slave->name,
                    'profile' => [],
                ];

                while ($row = $res->fetch_assoc()) {
                    $profile['profile'][] = [
                        'id' => $row['Query_ID'],
                        'time' => $row['Duration'],
                        'query' => $row['Query'],
                    ];
                }

                $profiles[] = $profile;
            }
        }

        return $profiles;
    }

    /**
     * Возвращает значение опции драйвера
     *
     * @param string $name Имя
     *
     * @return mixed
     */
    public function getOption($name)
    {
        if (isset($this->_driver_options[$name])) {
            return $this->_driver_options[$name];
        }

        return null;
    }

    /**
     * Возвращает значение опций драйвера
     *
     * @internal param $name Имя
     *
     * @return mixed
     */
    public function getOptions()
    {
        $this->_driver_options;
    }

    /**
     * Получение атрибута
     *
     * @param $attribute
     *
     * @return mixed
     */
    public function getAttribute($attribute)
    {
        if (isset($this->_attributes[$attribute])) {
            return $this->_attributes[$attribute];
        }

        if ($this->_object === null) {
            $this->__connect();
        }

        $this->_attributes[$attribute] = $this->_object->getAttribute($attribute);

        return $this->_attributes[$attribute];
    }

    /**
     * Отключается от сервера
     *
     * @return bool
     */
    public function close()
    {
        if ($this->_is_connected === true) {
            $this->_is_connected = false;
            $this->__disconnect();
        }

        return false;
    }

    /**
     * Проверяет подключение
     *
     * @return bool
     */
    public function ping()
    {
        if ($this->_is_connected === false) {
            return false;
        }

        return  false !== $this->_object->query('SET NAMES UTF8');
    }

    /**
     * Выбор БД
     *
     * @param string $dbname Имя базы
     *
     * @throws Lib_Exception_Runtime_Backtraced
     *
     * @return bool
     */
    public function select_db($dbname)
    {
        if ($this->_is_connected === false) {
            $this->__connect();
        }

        if ($this->ping() === false) {
            throw new Lib_Exception_Runtime_Backtraced("mysql error: (PING) mysql server '" . $this->_host_ip . "'  when selecting db: " . $dbname);
        }

        if (false === $this->__exec('USE `' . $dbname . '`')) {
            return false;
        }

        return true;
    }

    /**
     * Выполняет запрос
     *
     * @param $query
     *
     * @throws Lib_Exception_Runtime_Backtraced
     *
     * @return bool|Lib_DB_Adapter_Statement
     */
    public function query($query)
    {
        $args = func_get_args();

        if ($this->_object === null) {
            $this->__connect();
        } elseif (self::PING_BEFORE_QUERY && $this->ping() === false) {
            // try to reconnect manually
            $this->_object = null;
            $this->__connect();
        }

        $type = self::getQueryType($query);

        $res = false;

        for ($nattmpt = 0; $nattmpt <= self::NATTMPTS_ON_GONE_AWAY; $nattmpt++) {
            if ($type == 'SELECT' || $type == 'SHOW') {
                if (count($args) >= 4) {
                    $res = @$this->_object->query($query, $args[1], $args[2], $args[3]);
                } elseif (count($args) == 3) {
                    $res = @$this->_object->query($query, $args[1], $args[2]);
                } else {
                    $res = @$this->_object->query($query);
                }
            } else {
                $res = $this->_affected_rows = @$this->_object->exec($query);
            }

            if (false === $res && true !== self::PING_BEFORE_QUERY) {
                $errInfo = $this->_object->errorInfo();

                if ($errInfo[0] == 'HY000' && $errInfo[1] == '2006') { // MySQL server has gone away
                    $this->_object = null;
                    $this->__connect();

                    continue;
                }
                break; // Any other error
            } else {
                break;
            }
        }

        if ($res === false) {
            $errInfo = $this->_object->errorInfo();
            throw new Lib_Exception_Runtime_Backtraced('mysql error (' . $errInfo[0] . '): ' . $errInfo[1] . ' ' . $errInfo[2] . ' in query: ' . $query);
        }

        if ($res instanceof PDOStatement) {
            return new Lib_DB_Adapter_Statement($res, $this->_host_ip, $this->_dbname);
        }

        return true;
    }

    /**
     * Вызов процедуры, для одного результата, после работы необходимо почистить
     *
     * @param string $proc Имя процедуры
     *
     * @return Lib_DB_Adapter_Statement|bool
     */
    public function call($proc)
    {
        if ($this->_object === null) {
            $this->__connect();
        }

        $args = func_get_args();
        $args = array_slice($args, 2);

        foreach ($args as &$arg) {
            $arg = $this->_object->quote($arg);
        }

        $sql = 'CALL ' . $proc . '(' . implode(', ', $args) . ')';

        return $this->query($sql);
    }

    /**
     * Вызов процедуры, для одного результата и освобождение ресурсов
     * (прокси-функция для совместимости с emysqli)
     *
     * @param string $proc Имя процедуры
     *
     * @return Lib_DB_Adapter_Statement|bool
     */
    public function call_free($proc)
    {
        $args = func_get_args();

        return call_user_func_array([$this, 'call'], $args);
    }

    /**
     * Выполняет запрос и возвращает количество измененных строк
     *
     * @param $query
     *
     * @throws Lib_Exception_Runtime_Backtraced
     *
     * @return int|bool
     */
    private function __exec($query)
    {
        if ($this->_object === null) {
            $this->__connect();
        } elseif (self::PING_BEFORE_QUERY && $this->ping() === false) {
            // try to reconnect manually
            $this->_object = null;
            $this->__connect();
        }

        $res = false;

        for ($nattmpt = 0; $nattmpt <= self::NATTMPTS_ON_GONE_AWAY; $nattmpt++) {
            $res = @$this->_object->exec($query);

            if (false === $res && true !== self::PING_BEFORE_QUERY) {
                $errInfo = $this->_object->errorInfo();

                if ($errInfo[0] == 'HY000' && $errInfo[1] == '2006') { // MySQL server has gone away
                    $this->_object = null;
                    $this->__connect();

                    continue;
                }
                break; // Any other error
            } else {
                break;
            }
        }

        if ($res === false) {
            $errInfo = $this->_object->errorInfo();
            throw new Lib_Exception_Runtime_Backtraced('mysql error (' . $errInfo[0] . '): ' . $errInfo[1] . ' ' . $errInfo[2] . ' in query: ' . $query);
        }

        return $res;
    }

    /**
     * Подготавливает запрос к выполнению
     *
     * @throws Lib_Exception_Runtime_Backtraced
     *
     * @param $query
     *
     * @return bool|Lib_DB_Adapter_Statement
     */
    public function prepare($query)
    {
        if ($this->_object === null) {
            $this->__connect();
        }

        if (self::PING_BEFORE_QUERY && $this->ping() === false) {
            // try to reconnect manually
            $this->_object = null;
            $this->__connect();
        }

        $res = false;

        for ($nattmpt = 0; $nattmpt <= self::NATTMPTS_ON_GONE_AWAY; $nattmpt++) {
            $res = $this->_object->prepare($query);

            if (false === $res && true !== self::PING_BEFORE_QUERY) {
                $errInfo = $this->_object->errorInfo();

                if ($errInfo[0] == 'HY000' && $errInfo[1] == '2006') { // MySQL server has gone away
                    // try to reconnect manually
                    $this->_object = null;
                    $this->__connect();

                    continue;
                }
                break; // Any other error
            } else {
                break;
            }
        }

        if ($res === false) {
            $errInfo = $this->_object->errorInfo();
            throw new Lib_Exception_Runtime_Backtraced('mysql error (' . $errInfo[0] . '): ' . $errInfo[1] . ' ' . $errInfo[2] . ' in query: ' . $query);
        }

        return new Lib_DB_Adapter_Statement($res, $this->_host_ip, $this->_dbname);
    }

    /**
     * Проверяет наличие открытой транзакции
     *
     * @return bool
     */
    public function inTransaction()
    {
        if ($this->_object === null) {
            return false;
        }

        return $this->_object->inTransaction();
    }

    /**
     * Открывает транзакцию
     *
     * @return bool Возвращает TRUE в случае успешного завершения или FALSE в случае возникновения ошибки
     */
    public function beginTransaction()
    {
        if ($this->_object === null) {
            $this->__connect();
        } elseif (self::PING_BEFORE_QUERY && $this->ping() === false) {
            // try to reconnect manually
            $this->_object = null;
            $this->__connect();
        }

        $res = false;

        for ($nattmpt = 0; $nattmpt <= self::NATTMPTS_ON_GONE_AWAY; $nattmpt++) {
            $res = $this->_object->beginTransaction();

            if (false === $res && true !== self::PING_BEFORE_QUERY) {
                $errInfo = $this->_object->errorInfo();

                if ($errInfo[0] == 'HY000' && $errInfo[1] == '2006') { // MySQL server has gone away
                    $this->_object = null;
                    $this->__connect();

                    continue;
                }
                break; // Any other error
            } else {
                break;
            }
        }

        return $res;
    }

    /**
     * Сохраняет транзакцию
     *
     * @throws Lib_Exception_Runtime_Backtraced
     *
     * @return bool        Возвращает TRUE в случае успешного завершения или FALSE в случае возникновения ошибки
     */
    public function commit()
    {
        if ($this->_object === null) {
            $this->__connect();
        }

        return $this->_object->commit();
    }

    /**
     * Отменяет транзакцию
     *
     * @throws Lib_Exception_Runtime_Backtraced
     *
     * @return bool        Возвращает TRUE в случае успешного завершения или FALSE в случае возникновения ошибки
     */
    public function rollBack()
    {
        if ($this->_object === null) {
            $this->__connect();
        }

        return $this->_object->rollBack();
    }

    /**
     * Возвращает стандартизованный тип запроса
     *
     * @param string $query Запрос
     *
     * @return string
     */
    public static function getQueryType($query)
    {
        $query = trim($query);
        $type = 'OTHER';

        if (stripos($query, 'select') === 0) {
            $type = 'SELECT';
        } elseif (stripos($query, 'insert') === 0) {
            $type = 'INSERT';
        } elseif (stripos($query, 'update') === 0) {
            $type = 'UPDATE';
        } elseif (stripos($query, 'delete') === 0) {
            $type = 'DELETE ';
        } elseif (stripos($query, 'replace') === 0) {
            $type = 'REPLACE';
        } elseif (stripos($query, 'show') === 0) {
            $type = 'SHOW';
        }

        return $type;
    }

    /**
     * @param string $table
     *
     * @return bool
     */
    public function isTableExists($table)
    {
        return (bool) count(
            $this->query("SHOW TABLES LIKE '" . addcslashes($table, "\\'_%") . "'")->fetchAll(\PDO::FETCH_COLUMN)
        );
    }

    /**
     * @param string $table
     * @param string $column
     *
     * @return bool
     */
    public function isColumnExists($table, $column)
    {
        $sql = "SHOW COLUMNS FROM `$table` LIKE '" . addcslashes($column, "\\'_%") . "'";

        return $this->isTableExists($table)
        && (bool) count($this->query($sql)->fetchAll(\PDO::FETCH_COLUMN));
    }

    /**
     * @param string $table
     * @param string $index
     *
     * @return bool
     */
    public function isIndexExists($table, $index)
    {
        $res = $this->query('SELECT 1 FROM information_schema.statistics
			WHERE table_schema = \'' . addslashes($this->name) . '\'
				AND table_name = \'' . addslashes($table) . '\'
				AND index_name = \'' . addslashes($index) . '\' LIMIT 1');

        return (bool) ($res && $res->num_rows > 0);
    }

    /**
     * Геттер
     *
     * @throws Lib_Exception_Runtime_Backtraced
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if ($this->_object === null && ($name == 'insert_id' || $name == 'errno' || $name == 'error' || $name == 'affected_rows')) {
            throw new Lib_Exception_Runtime_Backtraced('DB is not connected');
        }

        switch ($name) {
            case 'insert_id':
                return $this->_object->lastInsertId();
            case 'errno':
                return $this->_object->errorCode();
            case 'error':
                $errInfo = $this->_object->errorInfo();

                return $errInfo[2];
            case 'affected_rows':
                return $this->_affected_rows;
            case 'host':
                return $this->_host;
            case 'host_ip':
                return $this->_host_ip;
            case 'port':
                return $this->_port;
            case 'name':
                return $this->_dbname;
            case 'user':
                return $this->_username;
            case 'pass':
                return $this->_passwd;
        }

        if ($this->_info === null || false === isset($this->_info[$name])) {
            return null;
        }

        return null;
    }
}
