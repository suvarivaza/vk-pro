<?php

/**
 *  Вывод и логирование отладочной информации и ошибок.
 */
class Lib_Trace
{
    /** Текстовый дамп логов */
    public const DUMP_PLAIN = 0;
    /** HTML-дамп логов  */
    public const DUMP_HTML = 1;

    private static $log = [];
    private static $start_time = 0;
    private static $start_memory = 0;
    private static $prev_time = 0;
    private static $prev_memory = 0;
    private static $started = false;
    private static $trace = false;
    private static $header = '<tr>
		<th colspan="2">time</th>
		<th rowspan="2">log</th>
		<th colspan="3">memory</th>
		</tr><tr>
		<th>total</th>
		<th>prev</th>
		<th>full</th>
		<th>total</th>
		<th>prev</th>
		</tr>';

    /**
     * Запускает отслеживание параметров выполнения приложения.
     * Засекается время начала работы и объекм используемой памяти.
     *
     * @static
     */
    public static function Start()
    {
        if (self::$started === true) {
            return;
        }
        self::$start_time = self::$prev_time = microtime(true);
        self::$start_memory = self::$prev_memory = memory_get_usage();
        self::$trace = true;
        self::$started = true;
        self::Log('start');
    }

    /**
     * Останавливает отслеживание параметров выполнения приложения.
     *
     * @static
     */
    public static function Stop()
    {
        self::$trace = false;
    }

    /**
     * Возвращает статус отслеживание параметров выполнения приложения.
     *
     * @static
     *
     * @return bool
     */
    public static function IsTracing()
    {
        return self::$trace;
    }

    /**
     * Логирует дамп переменной
     *
     * @static
     *
     * @param mixed $var Переменная
     * @param int $max_depth Максимальная глубина вывода.
     *         Определяет насколько глубоко нужно погружаться при формировании дампа свойст объектаов.
     */
    public static function VarDump($var, $max_depth = 10)
    {
        $_objects = [];
        self::Log(highlight_string("<?php\n" . self::_varDump($var, 0, $max_depth, $_objects), true));
    }

    private static function _varDump($var, $level, $depth, &$_objects)
    {
        $result = '';

        switch (gettype($var)) {
            case 'boolean':
                $result .= $var ? 'true' : 'false';
                break;
            case 'integer':
                $result .= $var;
                break;
            case 'double':
                $result .= $var;
                break;
            case 'string':
                $result .= "'" . $var . "'";
                break;
            case 'resource':
                $result .= '{resource}';
                break;
            case 'NULL':
                $result .= 'null';
                break;
            case 'unknown type':
                $result .= '{unknown}';
                break;
            case 'array':
                if ($depth <= $level) {
                    $result .= 'array(...)';
                } elseif (empty($var)) {
                    $result .= 'array()';
                } else {
                    $keys = array_keys($var);
                    $spaces = str_repeat("\t", $level);
                    $result .= 'array (';

                    foreach ($keys as $key) {
                        $result .= "\n" . $spaces . "\t'" . $key . "' => ";
                        $result .= self::_varDump($var[$key], $level + 1, $depth, $_objects);
                    }
                    $result .= "\n" . $spaces . '),';
                }
                break;
            case 'object':
                if (($id = array_search($var, $_objects, true)) !== false) {
                    $result .= get_class($var) . '#' . ($id + 1) . '(...)';
                } elseif ($depth <= $level) {
                    $result .= get_class($var) . '(...)';
                } else {
                    $id = array_push($_objects, $var);
                    $className = get_class($var);
                    $members = (array) $var;
                    $keys = array_keys($members);
                    $spaces = str_repeat(' ', $level * 4);
                    $result .= "$className#$id\n" . $spaces . '(';

                    foreach ($keys as $key) {
                        $keyDisplay = strtr(trim($key), ["\0" => ':']);
                        $result .= "\n" . $spaces . "\t'" . $keyDisplay . "' => ";
                        $result .= self::_varDump($members[$key], $level + 1, $depth, $_objects);
                    }
                    $result .= "\n" . $spaces . '),';
                }
                break;
        }

        return $result;
    }

    /**
     * Добавляет строку в логи.
     *
     * @static
     *
     * @param string $str
     */
    public static function Log($str)
    {
        if (self::$trace !== true) {
            return;
        }

        if (isset($_GET['show_call_place']) && $_GET['show_call_place'] == 'true') {
            $bt = debug_backtrace();

            if ($bt[0]['class'] != 'Trace') {
                $str = 'FILE: ' . $bt[0]['file'] . ':' . $bt[0]['line'] . '<br/>' . $str;
            } else {
                $str = 'FILE: ' . $bt[1]['file'] . ':' . $bt[1]['line'] . '<br/>' . $str;
            }
        }

        $m = memory_get_usage();
        $t = microtime(true);
        self::$log[] = [
            'time' => $t,
            'memory' => $m,
            'prev_time' => self::$prev_time,
            'prev_memory' => self::$prev_memory,
            'log' => $str,
        ];
        self::$prev_time = $t;
        self::$prev_memory = $m;
    }

    /**
     * Выводит в stderr трейс выполнения приложения.
     *
     * @static
     *
     * @param string $str Сообщение в логах.
     *         Сообщение, которое будев выведено первой строкой в трейс.
     * @param array|null $obj Трейс выполнения, который нужно вывести.
     *         Если параметр не задан - трейс будет получен при помощи debug_backtrace
     *
     * @return int
     */
    public static function BackTrace($str, $obj = null)
    {
        if (null === $obj) {
            $obj = debug_backtrace();
        }

        return self::_trace($str, $obj, false);
    }

    /**
     * Возвращает отформатированный трейс выполнения приложения.
     *
     * @static
     *
     * @param string $str Сообщение в логах.
     *         Сообщение, которое будев выведено первой строкой в трейс.
     * @param array|null $obj Трейс выполнения, который нужно вывести.
     *         Если параметр не задан - трейс будет получен при помощи debug_backtrace
     *
     * @return string
     */
    public static function BackTraceReturn($str, $obj = null)
    {
        if (null === $obj) {
            $obj = debug_backtrace();
        }

        return self::_trace($str, $obj, true);
    }

    /**
     * Вывод в stderr бэктрейса для исключения.
     *
     * @param Exception $e Исключение, для которого требуется вывести трейс.
     *
     * @return Exception Возвращает исходное исключение.
     */
    public static function BacktraceException(\Exception $e)
    {
        return static::_trace((string) $e, $e->getTrace());
    }

    /**
     * Возвращение бэктрейса для исключения.
     *
     * @param Exception $e Исключение, для которого требуется вернуть трейс.
     *
     * @return string Возвращает бектрейс.
     */
    public static function BacktraceExceptionReturn(\Exception $e)
    {
        return static::_trace((string) $e, $e->getTrace(), true);
    }

    private static function _trace($message, array $bt, $return = false)
    {
        $message .= (isset($_SERVER['REQUEST_METHOD']) && false === empty($_SERVER['REQUEST_METHOD']) ? '; Method: ' . $_SERVER['REQUEST_METHOD'] : '');
        $message .= (isset($_SERVER['HTTP_HOST']) ? ', URI: ' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']) : '') . ($_SERVER['REQUEST_URI'] ?? '');
        $message .= (isset($_SERVER['HTTP_REFERER']) && false === empty($_SERVER['HTTP_REFERER']) ? ', Referer: ' . $_SERVER['HTTP_REFERER'] : '');
        $message .= (isset($_SERVER['HTTP_USER_AGENT']) && false === empty($_SERVER['HTTP_USER_AGENT']) ? ', User-Agent: ' . $_SERVER['HTTP_USER_AGENT'] : '');

        return Lib_Trace::_backTrace('[' . date('Y-m-d H:i:s') . '] ' . $message, $bt, $return);
    }

    /**
     * Формирует форматированное текстовое представление трейса.
     *
     * @static
     *
     * @param string $str Сообщение в логах.
     * @param array $obj Трейс выполнения
     * @param bool $ret Вывести отформатированное текстовое представление трейса или сообщить его номер.
     *         true - Метод вернет отформатированное текстовое представление трейса
     *         false - Метод вернет номер трейса
     * @param int|null $pid Номер трейса.
     *         Если трейс уже сформирован ранее, но требуется сформировать для него сообщение.
     *
     * @return int|string
     */
    protected static function _backTrace($str, $obj, $ret = false, $pid = null)
    {
        $tab = '';
        $err = '';

        if (false === is_array($obj) || sizeof($obj) == 0) {
            return $err;
        }

        if (null === $pid) {
            $pid = rand(0, 9999);
        }
        $err = 'BT[' . $pid . ']: ' . $str;

        if (false === $ret) {
            error_log(addcslashes($err, "\n\r"));
        }
        $err .= "\n";

        foreach ($obj as $k => $v) {
            if (!isset($v['file'])) {
                $v['file'] = 'unknown';
            }

            if (!isset($v['line'])) {
                $v['line'] = 'unknown';
            }

            $kr = 'BT[' . $pid . '][' . $k . ']: file: ' . $v['file'] . ':' . $v['line'] . ' ';

            if ($k > 0 && isset($v['args']) && sizeof($v['args']) > 0) {
                $kr .= ($v['class'] ?? '') .
                    ($v['type'] ?? '') .
                    ($v['function'] ?? '') .
                    '(' . self::e_dump_args($v['args'], '', ',') . ')';
            } else {
                $kr .= $v['class'] . $v['type'] . $v['function'] . '()';
            }
            $tab .= "\t";

            if ($ret === false) {
                $kr = addcslashes(trim($kr, " \n\r"), "\n\r");

                if (false === empty($kr)) {
                    error_log($kr);
                }
            }

            $err .= $kr . "\n";
        }

        if ($ret === true) {
            return $err;
        } else {
            return $pid;
        }
    }

    private static function e_dump_args($var, $pref = '', $post = '')
    {
        $er = '';

        foreach ($var as $k => $v) {
            $er .= $pref . $k . ' => ';

            if (is_string($v)) {
                $er .= "'" . $v . "'";
            } elseif (is_null($v)) {
                $er .= 'NULL';
            } elseif (is_array($v)) {
                $er .= 'array(' . $post . self::e_dump_args($v, $pref . substr($pref, -1), $post) . $pref . ')';
            } elseif (is_object($v)) {
                $er .= '[Object]';
            } else {
                $er .= $v;
            }
            $er .= $post;
        }

        return $er;
    }

    /**
     * Формирует лог в html-формате
     *
     * @static
     *
     * @return string
     */
    public static function GetHTMLLog()
    {
        Lib_Trace::Log('end');
        $log = '';

        if (sizeof(self::$log)) {
            $log .= '<table style="width: 100%;">';
            $cnt = 0;
            $bgcolor = '#F0F0F0';

            foreach (self::$log as $l) {
                if ($cnt++ % 20 == 0) {
                    $log .= self::$header;
                }

                $log .= '<tr bgcolor="' . $bgcolor . '">';
                $log .= '<td width="80">' . number_format($l['time'] - self::$start_time, 6) . '</td>';
                $log .= '<td width="80">' . number_format($l['time'] - $l['prev_time'], 6) . '</td>';
                $log .= '<td>' . $l['log'] . '</td>';
                $log .= '<td width="80" align="right">' . number_format($l['memory'], 0, '.', ' ') . '</td>';
                $log .= '<td width="80" align="right">' . number_format($l['memory'] - self::$start_memory, 0, '.', ' ') . '</td>';
                $log .= '<td width="80" align="right">' . number_format($l['memory'] - $l['prev_memory'], 0, '.', ' ') . '</td>';

                if ($bgcolor == '#F0F0F0') {
                    $bgcolor = '#E0E0E0';
                } else {
                    $bgcolor = '#F0F0F0';
                }
            }
            $log .= '</table>';
        }
        Lib_Trace::Log('start');

        return $log;
    }

    /**
     * Формирует лог в plaintext-формате
     *
     * @static
     *
     * @return string
     */
    public static function GetPlainLog()
    {
        $log = '';

        if (sizeof(self::$log)) {
            Lib_Trace::Log('end');

            foreach (self::$log as $l) {
                $log .= number_format($l['time'] - self::$start_time, 6) . "\t";
                $log .= number_format($l['time'] - $l['prev_time'], 6) . "\t";
                $log .= $l['log'] . "\t";
                $log .= number_format($l['memory'], 0, '.', ' ') . "\t";
                $log .= number_format($l['memory'] - self::$start_memory, 0, '.', ' ') . "\t";
                $log .= number_format($l['memory'] - $l['prev_memory'], 0, '.', ' ') . "\n";
            }

            Lib_Trace::Log('start');
        }

        return $log;
    }

    /**
     * Возвращает время начала отслеживания приложения.
     *
     * @static
     *
     * @return int
     */
    public static function GetStartTime()
    {
        return self::$start_time;
    }

    /**
     * Сохраняет лог во временный файл
     *
     * @static
     *
     * @param string $prefix Папка логов (log)
     * @param int $type Тип лога (PLAIN|HTML)
     *
     * @return void
     */
    public static function Dump($prefix = 'log', $type = self::DUMP_PLAIN)
    {
        if ($type !== self::DUMP_PLAIN && $type !== self::DUMP_HTML) {
            return;
        }

        $current_time = time();
        $path = VAR_PATH . $prefix . '/' . date('Y/m/d/H', $current_time);

        if (!is_dir($path) && false === mkdir($path, 0777, true)) {
            error_log(__CLASS__ . '::' . __METHOD__ . '(): Can\'t create logs folder: ' . $path);

            return;
        }

        if ($type === self::DUMP_PLAIN) {
            $log = self::GetPlainLog();
        } else {
            $log = self::GetHTMLLog();
        }

        $ext = ($type === self::DUMP_PLAIN ? 'log' : 'html');
        $path = $path . '/' . date('i', $current_time) . '.' . $ext;

        if (file_put_contents($path, $log, FILE_APPEND) === false) {
            error_log(__CLASS__ . '::' . __METHOD__ . '(): Can\'t write logs to ' . $path);
        }
    }
}
