<?php

/**
 * Класс сокета
 * Управляет подключением к базе, реализует протокол Handler Socket
 *
 * @link https://github.com/ahiguti/HandlerSocket-Plugin-for-MySQL/blob/master/docs-en/protocol.en.txt
 * @link http://code.google.com/p/php-handlersocket/
 */
class Lib_HSocket_Socket
{
    public const ERR_INVALID_OPERATION = -1;
    public const ERR_CONNECTION_RESET_BY_PEER = -2;

    /**
     * Стандартные порты для подключения к handler socket api
     */
    public const READ_PORT = 9990;
    public const WRITE_PORT = 9999;

    /**
     * Идентификаторы сокетов чтения и записи
     */
    public const S_READ = 0;
    public const S_WRITE = 1;

    /**
     * Сокеты подключения к серверу MySQL (MariaDB)
     *
     * @var  resource
     */
    protected $_sockets = [null, null];

    /**
     * ip-адрес или имя сервера базы данных для подключения
     * Должен быть проинициализирован дочерним классом
     *
     * @var  string
     */
    protected $_host = '';
    protected $_host_ip = null;

    /**
     * Имя базы данных для подключения
     *
     * @var string
     */
    protected $_dbName = '';

    /**
     * Объект класса Lib_HSocket_Socket для работы со slave-сервером
     *
     * @var Lib_HSocket_Socket
     */
    public $slave = null;

    /**
     * Массив закешированных объектов Lib_HSocket_Index
     *
     * @var array
     */
    private $_indexes = [];

    /**
     * Идентификатор последнего созданного индекса
     *
     * @var int
     */
    private $_lastIndexID = -1;

    /**
     * Текущая кодировка
     *
     * @var string
     */
    private $_currentEncoding = null;

    /**
     * Кодировка базы
     *
     * @var string
     */
    private $_targetEncoding = null;

    /**
     * Время последнего подключения
     *
     * @var int
     */
    private $_lastConnected = null;

    /**
     * Конструктор класса, получает из Lib_DB_Factory информацию о базе данных
     *
     * @param \Database $dbName Имя базы
     * @param bool $master Флаг мастера
     * @param string $current_encoding Текущая кодировка БД
     * @param string $target_encoding Кодировка данной БД
     *
     * @throws Lib_HSocket_Exception_InvalidHost
     */
    public function __construct($dbName, $master = true, $current_encoding = null, $target_encoding = null)
    {
        $this->_dbName = $dbName->name;
        $this->_host = $dbName->host;
        $this->_host_ip = $dbName->host;

        $this->_currentEncoding = $current_encoding;
        $this->_targetEncoding = $target_encoding;
    }

    /**
     * Открытие сокета (подключения к серверу)
     *
     * @param int $sid Идентификатор сокета
     *
     * @throws Lib_HSocket_Exception_CanNotOpenSocket
     * @throws Lib_HSocket_Exception_InvalidHost
     */
    public function connect($sid = self::S_READ)
    {

        if ($sid == self::S_READ) {
            $port = self::READ_PORT;
        } elseif ($sid == self::S_WRITE) {
            $port = self::WRITE_PORT;
        } else {
            throw new Lib_HSocket_Exception_InvalidHost('Invalid socket id for ' . __CLASS__ . '::' . __METHOD__);
        }

        if (is_resource($this->_sockets[$sid])) {
            return;
        }

        if ($this->_host == '') {
            throw new Lib_HSocket_Exception_InvalidHost('Empty host.');
        }

        $this->_sockets[$sid] = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);


        if (false === socket_connect($this->_sockets[$sid], $this->_host, $port)) {
            $errno = socket_last_error($this->_sockets[$sid]);
            $errmsg = socket_strerror($errno);


            throw new Lib_HSocket_Exception_CanNotOpenSocket($this->_host, $port, $errno, $errmsg);
        }

        $this->_lastConnected = $end_block = microtime(true);
    }

    /**
     * Закрытие сокета (подключения к серверу)
     *
     * @param int $sid Идентификатор сокета
     *
     * @return bool
     */
    private function _disconnect($sid)
    {
        if (!is_resource($this->_sockets[$sid])) {
            return true;
        }

        socket_close($this->_sockets[$sid]);

        return true;
    }

    /**
     * Закрывает сокет и очищает всю память
     */
    public function close()
    {
        $this->_indexes = [];
        $this->_lastIndexID = -1;

        $this->_disconnect(self::S_READ);
        $this->_disconnect(self::S_WRITE);
    }

    /**
     * Запись данных в сокет
     *
     * @param string $data Данные для записи в сокет
     * @param int $sid Идентификатор сокета
     *
     * @throws Lib_HSocket_Exception_SocketGoneAway
     */
    private function write($data, $sid)
    {
        if (strlen($data) == 0) {
            return;
        }

        $err = 0;
        $charsWritten = socket_send($this->_sockets[$sid], $data, strlen($data), MSG_DONTROUTE);

        if ($charsWritten < strlen($data) || ($err = socket_get_option($this->_sockets[$sid], SOL_SOCKET, SO_ERROR)) || socket_last_error($this->_sockets[$sid])) {
            // reconnect
            $this->_disconnect($sid);
            throw new Lib_HSocket_Exception_SocketGoneAway($this->_host_ip, $this->_dbName, $err, $this->_lastConnected);
        }
    }

    /**
     * Чтение из сокета
     *
     * @param int $sid Идентификатор сокета
     * @param int $count Количество строк ответа
     *
     * @return array        Данные, прочитанные из сокета
     * @throws Lib_HSocket_Exception_SocketGoneAway
     *
     */
    private function read($sid, $count = 1)
    {
        $data = '';

        while (true) {
            $data .= $buf = socket_read($this->_sockets[$sid], 8192, PHP_BINARY_READ);

            if (!$buf) {
                $err = socket_last_error($this->_sockets[$sid]);
                $this->_disconnect($sid);
                throw new Lib_HSocket_Exception_SocketGoneAway($this->_host_ip, $this->_dbName, $err, $this->_lastConnected);
            } elseif (substr($buf, -1) == "\n" && ($count == 1 || $count == substr_count($data, "\n"))) {
                break;
            }
        }

        return $data;
    }

    /**
     * Производит кодирование строки параметров в соответствии с протоколом Handler Socket
     *
     * @param mixed $arg Параметр для кодирования
     *
     * @return string
     */
    private function encode($arg)
    {
        if (is_array($arg)) {
            foreach ($arg as $k => $a) {
                $arg[$k] = $this->encode($a);
            }
            $result = implode("\t", $arg);
        } else {
            if ($arg === null) {
                return "\x00";
            }

            $result = preg_replace_callback('/([\\x00-\\x0f])/', function ($matches) {
                return "\x01" . chr(ord($matches[1]) + 64);
            }, $arg);
        }

        return $result;
    }

    /**
     * Посылает серверу переданные параметры, разделённые табуляциями, принимает от сервера ответную строку, разбивает на массив по табуляциям и возвращает этот массив
     * ВАЖНО! Не вызывать снаружи
     *
     * @param array $query Массив с параметрами запроса
     * @param array $fields Массив с названиями полей
     * @param int $sid Идентификатор сокета
     *
     * @return array           $result                       Массив с результатами запроса.
     *          int              $result['errorcode']    код результата
     *          int              $result['numcolumn']    количество столбцов в одной строке результата
     *          array of string  $result[<число>]        строка результата номер <число>, массив строк со значениями полей. Если указан параметр $fields, то ассоциативный массив строк.
     * @throws Lib_HSocket_Exception_QueryError
     *
     * @throws Lib_HSocket_Exception_SlaveInvalidOperation
     */
    public function executeCommand($query, $fields = null, $sid = self::S_READ)
    {
        $queryString = $this->encode($query);

        $this->write($queryString . "\n", $sid);
        $raw_result = $this->read($sid);

        if (substr($raw_result, -1) == "\n") {
            $raw_result = substr($raw_result, 0, -1);
        }

        $res = explode("\t", $raw_result);
        reset($res);

        $result_code = current($res);

        if ($result_code != 0) {
            throw new Lib_HSocket_Exception_QueryError($queryString, ($res[2] ?: 'unknown error'), $res[0]);
        }
        $result_numcolumn = next($res);

        $rowsCount = 0;
        $result = [];
        $element = next($res);

        while ($element !== false) {
            $row = [];

            if (is_array($fields)) {
                for ($i = 0; $i < $result_numcolumn; $i++) {
                    $row[$fields[$i]] = ($element === chr(0) ? null : preg_replace_callback('/(\\x01)([\\x40-\\x4f])/', function ($matches) {
                        return chr(ord($matches[2]) - 64);
                    }, $element));
                    $element = next($res);
                }
            } else {
                for ($i = 0; $i < $result_numcolumn; $i++) {
                    $row[] = ($element === chr(0) ? null : preg_replace_callback('/(\\x01)([\\x40-\\x4f])/', function ($matches) {
                        return chr(ord($matches[2]) - 64);
                    }, $element));
                    $element = next($res);
                }
            }

            $result[$rowsCount] = $row;
            $rowsCount++;
        }

        return $result;
    }

    /**
     * Посылает серверу несколько строк запросов, принимает от сервера ответные строки и возвращает значения из этих строк
     * ВАЖНО! Не вызывать снаружи
     *
     * @param array $queries Массив запросов
     * @param array $fields Массив с названиями полей
     * @param int $sid Идентификатор сокета
     *
     * @return array            $result               Массив с результатами запроса.
     *          int             $result['errorcode']  код результата
     *          int                $result['numcolumn']  количество столбцов в одной строке результата
     *          array of string    $result[<число>]      строка результата номер <число>, массив строк со значениями полей. Если указан параметр $fields, то ассоциативный массив строк.
     * @throws Lib_HSocket_Exception_QueryError
     *
     */
    public function executeMulti($queries, $fields = null, $sid = self::S_READ)
    {
        $queryString = '';

        foreach ($queries as $query) {
            $queryString .= $this->encode($query) . "\n";
        }

        $this->write($queryString, $sid);
        $response = $this->read($sid, count($queries));

        $result = [];
        $response_rows = explode("\n", $response);

        foreach ($response_rows as $response_row) {
            $res = explode("\t", $response_row);
            reset($res);

            $result_code = current($res);

            if ($result_code != 0) {
                throw new Lib_HSocket_Exception_QueryError($queryString, $result);
            }
            $result_numcolumn = next($res);

            $element = next($res);

            while ($element !== false) {
                $row = [];

                if (is_array($fields)) {
                    for ($i = 0; $i < $result_numcolumn; $i++) {
                        $row[$fields[$i]] = ($element === chr(0) ? null : preg_replace_callback('/(\\x01)([\\x40-\\x4f])/', function ($matches) {
                            return chr(ord($matches[2]) - 64);
                        }, $element));
                        $element = next($res);
                    }
                } else {
                    for ($i = 0; $i < $result_numcolumn; $i++) {
                        $row[] = ($element === chr(0) ? null : preg_replace_callback('/(\\x01)([\\x40-\\x4f])/', function ($matches) {
                            return chr(ord($matches[2]) - 64);
                        }, $element));
                        $element = next($res);
                    }
                }
                $result[] = $row;
            }
        }

        if ((isset($_GET['log_db']) && $_GET['log_db'] == 'true') || (isset($_GET['log_db_trace']) && $_GET['log_db_trace'] == 'true')) {
            $log = '';

            if (isset($_GET['log_db_trace']) && $_GET['log_db_trace'] == 'true') {
                $bt = debug_backtrace();
                $log .= 'FILE: ' . $bt[0]['file'] . ':' . $bt[0]['line'] . '<br/>';
            }
            $log .= 'SOCKET QUERY: ' . $this->_host_ip . ':' . $this->_dbName . "\n\t\t\t\t" . $queryString;
            Lib_Trace::Log($log);
        }

        return $result;
    }

    /**
     * Возвращает объект класса Lib_HSocket_Index
     *
     * @param string $table Название таблицы
     * @param string $indexName Название индекса, первичный обозначается ключевым словом 'PRIMARY'
     * @param mixed $fields Название поля (string) или массив с названиями полей (array of string), которые следует возвращать методом Lib_HSocket_Index::Select
     *
     * @return Lib_HSocket_Index    Объект класса Lib_HSocket_Index
     */
    public function openIndex($table, $indexName, $fields)
    {
        $fields = (array)$fields;
        $uniqID = md5($table . $indexName . implode('', $fields));

        if (isset($this->_indexes[$uniqID])) {
            return $this->_indexes[$uniqID];
        }

        // создать новый индекс в handler socket
        $this->_lastIndexID++;
        $this->_indexes[$uniqID] = new Lib_HSocket_Index($this, $table, $this->_lastIndexID, $indexName, $fields);

        return $this->_indexes[$uniqID];
    }

    /**
     * Возвращает имя базы данных
     *
     * @return string
     */
    public function getDbName()
    {
        return $this->_dbName;
    }
}
