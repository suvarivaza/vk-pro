<?php

namespace System;

/**
 * Класс Http-запроса.
 *
 * @property HttpRequest_Trait|HttpRequest_Trait[] $server
 * @property HttpRequest_Trait|HttpRequest_Trait[] $post
 * @property HttpRequest_Trait|HttpRequest_Trait[] $get
 * @property HttpRequest_Trait|HttpRequest_Trait[] $cookie
 * @property HttpRequest_Trait|HttpRequest_Trait[] $request
 * @property array $rawCookie
 * @property array $rawGet
 * @property array $rawPost
 * @property array $rawRequest
 * @property array $rawServer
 * @property string $method
 *
 * @todo shell request class (not based on httpMessage).
 *
 * @package System
 */
class HttpRequest extends HttpMessage
{
    public const INCLUDE_VARS = 0;
    public const EXCLUDE_VARS = 1;

    public const SIGNED_NUM = 0x00000002;
    public const UNSIGNED_NUM = 0x00000004;
    public const INTEGER_NUM = 0x00000006;
    public const DECIMAL_NUM = 0x00000008;
    public const ALPHA = 0x00000010;
    public const ALPHA_NUM = 0x00000020;
    public const EMAIL = 0x00000040;
    public const URL = 0x00000080;
    public const UUID = 0x00000100;
    public const VALUE = 0x00000200;
    public const DATETIME = 0x0000400;
    public const STRING = 0x0000800;

    public const OUT_HTML = 0x00004000;
    public const OUT_HTML_AREA = 0x00008000;
    public const OUT_HTML_CLEAN = 0x00010000;
    public const OUT_CHANGE_NL = 0x00020000;
    public const OUT_CHANGE_TAGS = 0x00040000;
    public const OUT_CHANGE_QUOTES = 0x00080000;
    public const OUT_REMOVE_NL = 0x00100000;
    public const OUT_HTML_CLEAR_SKYPE = 0x00200000;
    public const OUT_REMOVE_NON_PRINT_CHARS = 0x00400000;
    public const OUT_IGNORE_SPACES = 0x01000000;

    public const GET = 'GET';
    public const HEAD = 'HEAD';
    public const POST = 'POST';
    public const PUT = 'PUT';
    public const DELETE = 'DELETE';
    public const OPTIONS = 'OPTIONS';

    private $_method;

    /** @var \Lib_Url $url */
    private $_url;

    /**
     * @name Объекты трейтов
     * @{
     */
    /** @var HttpRequest_Trait|HttpRequest_Trait[] $_postObject */
    private $_postObject;
    /** @var HttpRequest_Trait|HttpRequest_Trait[] $_getObject */
    private $_getObject;
    /** @var HttpRequest_Trait|HttpRequest_Trait[] $_cookieObject */
    private $_cookieObject;
    /** @var HttpRequest_Trait|HttpRequest_Trait[] $_serverObject */
    private $_serverObject;
    /** @} */

    /**
     * AS корпоративной сети.
     *
     * @var string
     */
    private static $_corporateASNum = 'AS197200';

    /**
     * Доверенные AS поисковых систем.
     *
     * @var array
     */
    private static $_trustedASNums = [
        'AS197200', // LLC "Internet Tehnologii"
        'AS13238', // Yandex
        'AS36647', // Yahoo
        'AS15169', // Google
        'AS24638', // Rambler
        'AS47764', // Mail.ru
        'AS8075', // Microsoft
        'AS44128', // Internet-PRO
        'AS14778', // Inktomi Corporation
        'AS49281', // M-100
    ];

    private static $_incomingRequest;

    /**
     * Создаёт объект по фактическим параметрам запроса в $_GET, $_POST, etc.
     *
     * @return HttpRequest
     */
    public static function IncomingHttpRequest()
    {
        if (isset(self::$_incomingRequest)) {
            return self::$_incomingRequest;
        }

        $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] . DOMAIN_SUFFIX;
        $path = isset($_SERVER['REQUEST_URI']) ? ltrim($_SERVER['REQUEST_URI'], '/') : '';

        /* Hack for server ip. */
        $_SERVER['SERVER_ADDR'] = gethostbyname(php_uname('n'));

        /** @var HttpRequest $request */
        $request = new static(
            "$scheme://$host/$path",
            $_GET,
            [],
            $_COOKIE,
            $_SERVER
        );

        if (in_array($request->method, [static::POST])) {
            if (empty($_POST)) {
                $request->setBody(file_get_contents('php://input'));
            } else {
                $request->post = $_POST;
            }
        }

        return self::$_incomingRequest = $request;
    }

    /**
     * Создаёт объект запроса из параметров командной строки.
     *
     * @return HttpRequest
     */
    public static function IncomingShellRequest()
    {
        $script = $_SERVER['argv'][0];
        $get = [];

        foreach (array_slice($_SERVER['argv'], 1) as $arg) {
            list($key, $value) = explode(
                '=',
                $arg
            );

            $get[$key] = $value;
        }

        /* Hack for server ip. */
        $_SERVER['SERVER_ADDR'] = gethostbyname(php_uname('n'));

        return new static(
            $script,
            $get,
            [],
            [],
            $_SERVER
        );
    }

    public function __construct($url, array $get = null, array $post = [], array $cookie = [], array $server = [])
    {
        $this->setUrl($url);

        // Do not overwrite URL query if $get is not explicitly given.
        if (!is_null($get)) {
            $this->get = $get;
        }

        $this->post = $post;
        $this->cookie = $cookie;
        $this->server = $server;
    }

    /**
     * Установка кодировки для получения аргментов.
     *
     * Если внутренняя и внешняя кодировки не совпадают - будет установлен флаг перекодировки
     * и аргументы будут переводится из внутренней кодировки во внешнюю, если параметр
     * $encode равен true.
     *
     * @param string $encoding Кодировка
     * @param bool $encode Требуется ли перекодировка, или аргументы поставляется уже в нужной кодировке
     *
     * @return HttpRequest $this
     */
    public function setExternalEncoding($encoding, $encode = true)
    {
        parent::setExternalEncoding($encoding, $encode);

        $this->get->setExternalEncoding($encoding, $this->isEncode());
        $this->post->setExternalEncoding($encoding, $this->isEncode());
        $this->cookie->setExternalEncoding($encoding, $this->isEncode());
        $this->server->setExternalEncoding($encoding, $this->isEncode());

        return $this;
    }

    /**
     * Возвращает UID пользователя из Cookie
     *
     * @static
     *
     * @return string
     */
    public function getUid()
    {
        if (isset($this->cookie['uid'])) {
            return $this->cookie['uid']->string();
        }

        $res = '';

        foreach ($this->cookie->asArray() as $k => $v) {
            if (substr($k, 0, 4) == 'uid_') {
                $res = $v;
                break;
            }
        }

        return $res;
    }

    /**
     * Проверка того, что запрос пришел от нашего сервера.
     *
     * @return bool
     */
    public function isOurServer()
    {
        if (DC_NAME === 'dev') {
            return true;
        }

        if ($this->server['HTTP_X_IS_OUR_SERVER']->int(0) == 1) {
            return true;
        }

        return false;
    }

    /**
     * Проверка того, что запрос пришел от нашего сервера или c доверенным номером автономной системы.
     *
     * @return bool
     */
    public function isTrustedAS()
    {
        if ($this->isOurServer() || in_array($this->server['HTTP_X_AS_NUM']->string('none'), self::$_trustedASNums)) {
            return true;
        }

        return false;
    }

    /**
     * Проверка того, что запрос прищёл из корпоративной сети
     *
     * @return bool
     */
    public function isCorporate()
    {
        if ($this->isOurServer() || $this->server['HTTP_X_AS_NUM']->string('none') == self::$_corporateASNum) {
            return true;
        }

        return false;
    }

    /**
     * Возвращает geo-информацию по ip, с которого получен запрос
     *
     * @return array
     */
    public function geo()
    {
        if (DC_NAME === 'dev') {
            return [
                'Country' => 'RU',
                'RegID' => '74',
                'Lat' => null,
                'Long' => null,
            ];
        }

        if (false === isset($this->server['HTTP_X_GEO_LOCATION']) || empty($this->server['HTTP_X_GEO_LOCATION']) || $this->server['HTTP_X_GEO_LOCATION'] == '-') {
            return false;
        }

        $result = explode(',', $this->server['HTTP_X_GEO_LOCATION']);

        return [
            'Country' => $result[0] ?? null,
            'RegID' => $result[1] ?? null,
            'Lat' => $result[2] ?? null,
            'Long' => $result[3] ?? null,
        ];
    }

    /**
     * Я КуА
     *
     * @return bool
     */
    public function isQARobot()
    {
        if ($this->cookie['QA_ALLOW']->int(0, self::UNSIGNED_NUM) <= 0) {
            return false;
        }

        if (false !== strpos($this->server['REMOTE_ADDR'], '195.206.240.34')) {
            // Офис на Роднике
            return true;
        }

        return DC_NAME === 'dev';
    }

    /**
     * Я Аякс
     *
     * @return bool
     */
    public function isAjax()
    {
        return $this->server['HTTP_X_REQUESTED_WITH']->string() == 'XMLHttpRequest';
    }

    /**
     * Логирование запроса
     */
    public function log()
    {
        list($usec, $sec) = explode(' ', microtime());

        $requestId = $sec . '_' . $usec . $_SERVER['UNIQUE_ID'];

        $root = LOG_PATH . 'request/';
        $path = LOG_PATH . 'request/' . date('Y/m/d/H/i', time());

        if (!is_dir($path) && false === mkdir($path, 0777, true)) {
            return;
        }

        $request = [
            'UserID' => (int) \App::$UserID,
            'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],
            'REQUEST_TIME' => $_SERVER['REQUEST_TIME'],
            'HTTP_HOST' => $_SERVER['HTTP_HOST'],
            'REDIRECT_URL' => $_SERVER['REDIRECT_URL'],
            '_GET' => [],
            '_POST' => [],
            '_FILES' => [],
        ];

        $request['_POST'] = $_POST;
        $request['_GET'] = $_GET;

        /**
         * Rebuild $_FILES array.
         */
        foreach ($_FILES as $name => $data) {
            $request['_FILES'][$name] = [];

            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $i => $innerVal) {
                        $request['_FILES'][$name][$i][$key] = $innerVal;
                    }
                } else {
                    $request['_FILES'][$name][$key] = $value;
                }
            }
        }

        $cb = function (array &$file, $path) {
            if (is_file($file['tmp_name']) && copy($file['tmp_name'], $path . '/' . $file['name'])) {
                $file['tmp_name'] = $path . '/' . $file['name'];
            } else {
                $file['tmp_name'] = null;
            }
        };

        foreach ($request['_FILES'] as $form => &$file) {
            if (isset($file['name'])) {
                $cb($file, $root . $path . '/' . $form);
            } else {
                foreach ($file as $i => &$innerfile) {
                    $cb($innerfile, $root . $path . '/' . $form . '/' . $i);
                }
            }
        }

        file_put_contents($root . $path . '/' . $requestId . '.php', serialize($request));
        file_put_contents($root . '/request.log', $request['REQUEST_TIME'] . ' ' . $path . PHP_EOL, FILE_APPEND);
    }

    protected function _prepareTrait($data)
    {
        if (is_array($data)) {
            $data = new \System\HttpRequest_Trait($data);
        }

        if (!($data instanceof \System\HttpRequest_Trait) || $data->asArray() === null) {
            throw new \Lib_Exception_InvalidArgument(
                '$data must be an array or \System\HttpRequest_Trait containing array.'
            );
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        if (empty($this->_method)) {
            $this->_method = $this->server['REQUEST_METHOD']->string(self::GET);
        }

        return $this->_method;
    }

    /**
     * @param string $method
     *
     * @return $this
     *
     * @throws \Lib_Exception_InvalidArgument_Type
     */
    public function setMethod($method)
    {
        if (!is_string($method)) {
            throw new \Lib_Exception_InvalidArgument_Type($method, 'string');
        }

        $this->_method = $method;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return (string) $this->_url;
    }

    /**
     * @param string|\Lib_Url $url
     *
     * @return $this
     *
     * @throws \Lib_Exception_InvalidArgument
     */
    public function setUrl($url)
    {
        if (is_string($url)) {
            $url = new \Lib_Url($url);
        }

        if (!($url instanceof \Lib_Url)) {
            throw new \Lib_Exception_InvalidArgument(
                '$url must be a string containing valid url or \Lib_Url object.'
            );
        }

        if ($url->isMalformed()) {
            throw new \Lib_Exception_InvalidArgument('Expecting valid URL, malformed given.');
        }

        // This is a _http_ request, right?
        if (!empty($url->sheme) && !in_array($url->sheme, ['http', 'https'])) {
            throw new \Lib_Exception_InvalidArgument('Expecting URL with "http(s)" scheme; "' . $url->sheme . '" given.');
        }

        $this->_url = $url;
        $this->get = $url->query;

        return $this;
    }

    public function __get($name)
    {
        switch ($name) {
            case 'cookie':
                return $this->_cookieObject;

            case 'get':
                return $this->_getObject;

            case 'post':
                return $this->_postObject;

            case 'request':
                if ($this->method == self::POST || $this->method == self::PUT) {
                    return $this->_postObject;
                } else {
                    return $this->_getObject;
                }

                // no break
            case 'server':
                return $this->_serverObject;

            case 'method':
                return $this->getMethod();

            case 'rawCookie':
                return $this->cookie->asArray();

            case 'rawGet':
                return $this->get->asArray();

            case 'rawPost':
                return $this->post->asArray();

            case 'rawRequest':
                return $this->request->asArray();

            case 'rawServer':
                return $this->server->asArray();

            default:
                throw new \Lib_Exception_UnknownProperty($name, __CLASS__);
        }
    }

    public function __set($name, $value)
    {
        switch ($name) {
            case 'cookie':
                $this->_cookieObject = $this->_prepareTrait($value);
                break;

            case 'get':
                $this->_getObject = $this->_prepareTrait($value);
                $this->_url->query = $this->_getObject->asArray([]);
                break;

            case 'post':
                $this->_postObject = $this->_prepareTrait($value);

                // using parent intentionally here, bad design
                parent::setBody(
                    http_build_query($this->_postObject->asArray([]))
                );

                break;

            case 'server':
                $this->_serverObject = $this->_prepareTrait($value);
                break;

            case 'method':
                $this->setMethod($value);
                break;

            default:
                throw new \Lib_Exception_UnknownProperty($name, __CLASS__);
        }
    }

    public function __sleep()
    {
        throw new \Lib_Exception_Domain(__CLASS__ . ' can not be serialized.');
    }

    public function setBody($body)
    {
        try {
            $this->post = $body;
        } catch (\Lib_Exception_InvalidArgument $e) {
            $this->post = [];

            parent::setBody(
                $body
            );
        }

        return $this;
    }
}
