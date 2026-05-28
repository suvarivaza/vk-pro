<?php

namespace System;

/**
 * Класс для создания HTTP ответов.
 *
 * Класс используется для создания полностью буферизованных http-ответов, включая заголовки и cookies.
 * Имеются методы для управления кэшированием на стороне клиента.
 *
 * @class HttpResponse
 *
 * @package System
 */
class HttpResponse extends HttpMessage
{
    /** Префикс мета-заголовков http-ответа */
    public const X_META_PREFIX = 'X-Meta';

    /**
     * @name Мета -заголовки http-ответа
     * @{
     */
    public const X_META_CACHE_KEY = 'X-Meta-Cache-Key';
    public const X_META_CACHE_LIFETIME = 'X-Meta-Cache-Lifetime';
    public const X_META_STYLES = 'X-Meta-Styles';
    public const X_META_SCRIPTS = 'X-Meta-Scripts';
    /** @} */
    public const S1_CONTINUE = 100;
    public const S1_SWITCHING_PROTOCOLS = 101;
    public const S1_PROCESSING = 102;
    public const S1_CHECKPOINT = 103;
    public const S1_REQUEST_URI_TOO_LONG = 122;

    public const S2_OK = 200;
    public const S2_CREATED = 201;
    public const S2_ACCEPTED = 202;
    public const S2_NON_AUTHORITATIVE_INFORMATION = 203;
    public const S2_NO_CONTENT = 204;
    public const S2_RESET_CONTENT = 205;
    public const S2_PARTIAL_CONTENT = 206;
    public const S2_MULTI_STATUS = 207;
    public const S2_ALREADY_REPORTED = 208;
    public const S2_IM_USED = 226;

    public const S3_MULTIPLE_CHOICES = 300;
    public const S3_MOVED_PERMANENTLY = 301;
    public const S3_FOUND = 302;
    public const S3_SEE_OTHER = 303;
    public const S3_NOT_MODIFIED = 304;
    public const S3_USE_PROXY = 305;
    public const S3_SWITCH_PROXY = 306;
    public const S3_TEMPORARY_REDIRECT = 307;
    public const S3_RESUME_INCOMPLETE = 308;

    public const S4_BAD_REQUEST = 400;
    public const S4_UNAUTHORIZED = 401;
    public const S4_PAYMENT_REQUIRED = 402;
    public const S4_FORBIDDEN = 403;
    public const S4_NOT_FOUND = 404;
    public const S4_METHOD_NOT_ALLOWED = 405;
    public const S4_NOT_ACCEPTABLE = 406;
    public const S4_PROXY_AUTHENTICATION_REQUIRED = 407;
    public const S4_REQUEST_TIMEOUT = 408;
    public const S4_CONFLICT = 409;
    public const S4_GONE = 410;
    public const S4_LENGTH_REQUIRED = 411;
    public const S4_PRECONDITION_FAILED = 412;
    public const S4_REQUEST_ENTITY_TOO_LARGE = 413;
    public const S4_REQUEST_URI_TOO_LONG = 414;
    public const S4_UNSUPPORTED_MEDIA_TYPE = 415;
    public const S4_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    public const S4_EXPECTATION_FAILED = 417;
    public const S4_I_M_A_TEAPOT = 418;
    public const S4_ENHANCE_YOUR_CALM = 420;
    public const S4_UNPROCESSABLE_ENTITY = 422;
    public const S4_LOCKED = 423;
    public const S4_FAILED_DEPENDENCY = 424;
    public const S4_UPGRADE_REQUIRED = 426;
    public const S4_PRECONDITION_REQUIRED = 428;
    public const S4_TOO_MANY_REQUESTS = 429;
    public const S4_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    public const S4_NO_RESPONSE = 444;
    public const S4_RETRY_WITH = 449;
    public const S4_BLOCKED_BY_WINDOWS_PARENTAL_CONTROLS = 450;
    public const S4_WRONG_EXCHANGE_SERVER = 451;
    public const S4_CLIENT_CLOSED_REQUEST = 499;

    public const S5_INTERNAL_SERVER_ERROR = 500;
    public const S5_NOT_IMPLEMENTED = 501;
    public const S5_BAD_GATEWAY = 502;
    public const S5_SERVICE_UNAVAILABLE = 503;
    public const S5_GATEWAY_TIMEOUT = 504;
    public const S5_HTTP_VERSION_NOT_SUPPORTED = 505;
    public const S5_VARIANT_ALSO_NEGOTIATES = 506;
    public const S5_INSUFFICIENT_STORAGE = 507;
    public const S5_LOOP_DETECTED = 508;
    public const S5_BANDWIDTH_LIMIT_EXCEEDED = 509;
    public const S5_NOT_EXTENDED = 510;
    public const S5_NETWORK_AUTHENTICATION_REQUIRED = 511;
    public const S5_NETWORK_READ_TIMEOUT_ERROR = 598;
    public const S5_NETWORK_CONNECT_TIMEOUT_ERROR = 599;

    public const S_PERMANENT_REDIRECT = 301;
    public const S_SEE_OTHER = 303;
    public const S_TEMPORARY_REDIRECT = 307;

    /**
     * Статусы ответов.
     *
     * @link http://httpstatus.es/
     * @link http://upload.wikimedia.org/wikipedia/commons/6/65/Http-headers-status.gif?uselang=ru
     *
     * @var array
     */
    private static $_status_codes = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing', // (WebDAV) (RFC 2518)
        103 => 'Checkpoint',
        122 => 'Request-URI too long',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information', // (since HTTP/1.1)
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status', // (WebDAV) (RFC 4918)
        208 => 'Already Reported', // (WebDAV) (RFC 5842)
        226 => 'IM Used', // (RFC 3229)
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy', // (since HTTP/1.1)
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect', // (since HTTP/1.1)
        308 => 'Resume Incomplete',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot', // (RFC 2324)
        420 => 'Enhance Your Calm',
        422 => 'Unprocessable Entity', // (WebDAV) (RFC 4918)
        423 => 'Locked', // (WebDAV) (RFC 4918)
        424 => 'Failed Dependency', // (WebDAV) (RFC 4918)
        426 => 'Upgrade Required', // (RFC 2817)
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        444 => 'No Response',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        451 => 'Wrong Exchange server',
        499 => 'Client Closed Request',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates', // (RFC 2295)
        507 => 'Insufficient Storage', // (WebDAV) (RFC 4918)
        508 => 'Loop Detected', // (WebDAV) (RFC 5842)
        509 => 'Bandwidth Limit Exceeded', // (Apache bw/limited extension)
        510 => 'Not Extended', // (RFC 2774)
        511 => 'Network Authentication Required',
        598 => 'Network read timeout error',
        599 => 'Network connect timeout error',
    ];

    /** Статус ответа по умолчанию */
    public const DEFAULT_STATUS = 200;

    /** Тип контента по умолчанию */
    public const DEFAULT_CONTENT_TYPE = 'text/html';

    /** @var int Код статуса ответа */
    private $_status;

    /** @var string Тип контента */
    private $_content_type;

    /** @var string ETag */
    private $_etag;

    /** @var int Дата модификации */
    private $_last_modified;

    /**
     * Создает объект ответа с заданным телом и статусом.
     *
     * @param string $body Тело ответа
     * @param string $content_type Тип ответа
     *
     * @throws \Lib_Exception_InvalidArgument_Type
     * @throws \Lib_Exception_InvalidArgument
     */
    public function __construct($body = null, $content_type = self::DEFAULT_CONTENT_TYPE)
    {
        if (null !== $body && false === is_string($body)) {
            throw new \Lib_Exception_InvalidArgument_Type($body, 'string');
        }

        if (false === is_string($content_type)) {
            throw new \Lib_Exception_InvalidArgument_Type($content_type, 'string');
        }
        $this->_body = $body;
        $this->_status = self::DEFAULT_STATUS;
        $this->_content_type = $content_type;
        $this->noCache(); // По умолчанию не кэшировать ответ
        $this->setHeader('Content-Type', $this->_content_type . '; charset=' . $this->_external_encoding);
    }

    /**
     * @param string $url
     * @param int    $status
     *
     * @return HttpResponse
     */
    public function setLocation($url, $status = self::S3_FOUND)
    {
        $this->setHeader('Location', $url);

        if ($status) {
            $this->setStatus($status);
        }

        return $this;
    }

    /**
     * @param mixed $data
     *
     * @return HttpResponse
     */
    public function setJson($data)
    {
        /* @var HttpResponse $response */
        if (strcasecmp(static::$_internal_encoding, 'utf-8') != 0) {
            mb_convert_variables('utf-8', static::$_internal_encoding, $data);
        }

        $this->setContentType('application/json');
        $this->setExternalEncoding('utf-8');
        $this->setBody(\Lib_Json::encode($data));

        return $this;
    }

    /**
     * Получение внутренней кодировки, в которой работает приложение.
     *
     * @static
     *
     * @return string
     */
    public static function GetInternalEncoding()
    {
        return self::$_internal_encoding;
    }

    /**
     * Получение внешней кодировки, в которой производится выдача ответа.
     *
     * @return string
     */
    public function getExternalEncoding()
    {
        return $this->_external_encoding;
    }

    /**
     * Установка кодировки для выдачи ответа.
     * Если внутренняя и внешняя кодировки не совпадают - будет установлен флаг перекодировки
     * и тело ответа на выдаче будет переводится из внутренней кодировки во внешнюю, если параметр
     * $encode равен true.
     *
     * @param string $encoding Кодировка
     * @param bool $encode Требуется ли перекодировка, или контент поставляется уже в нужной кодировке
     *
     * @return HttpResponse $this
     */
    public function setExternalEncoding($encoding, $encode = true)
    {
        parent::setExternalEncoding($encoding, $encode);
        $this->setHeader('Content-Type', $this->_content_type . '; charset=' . $this->_external_encoding);

        return $this;
    }

    /**
     * Устанавливает тип содержимого ответа.
     *
     * @param string $content_type
     *
     * @return HttpResponse $this
     *
     * @throws \Lib_Exception_InvalidArgument_Type
     * @throws \Lib_Exception_InvalidArgument
     */
    public function setContentType($content_type)
    {
        if (false === is_string($content_type)) {
            throw new \Lib_Exception_InvalidArgument_Type($content_type, 'string');
        }
        $this->_content_type = $content_type;
        $this->setHeader('Content-Type', $this->_content_type . '; charset=' . $this->_external_encoding);

        return $this;
    }

    /**
     * Получение типа содержимого ответа.
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->_content_type;
    }

    /**
     * Отключение кэширования установкой заголовков.
     *
     * @return HttpResponse $this
     */
    public function noCache()
    {
        $this->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0'); // HTTP/1.1
        $this->setHeader('Pragma', 'no-cache');
        $this->setHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        $this->setHeader('Last-Modified', date('r', time() - 86400));

        return $this;
    }

    /**
     * Включение кеширования установкой заголовков.
     * Устанавливает заголовок Cache-Control в 'private', очищает заголовки Cache-Control, Pragma и Expires.
     * Если установлены ETag и/или LastModified, устанавливает также и эти заголовки.
     *
     * @return HttpResponse $this
     */
    public function enableCaching()
    {
        $this->setHeader('Cache-Control', 'private');
        $this->setHeader('Pragma', '');
        $this->setHeader('Expires', '');

        if (null !== $this->_etag) {
            $this->setHeader('Etag', $this->_etag);
        }

        if (null !== $this->_last_modified) {
            $now = time();

            if ($this->_last_modified > $now) {
                $this->_last_modified = $now;
            }
            $this->setHeader('Last-Modified', gmdate('D, d M Y H:i:s T', $this->_last_modified));
        }

        return $this;
    }

    /**
     * Установка статуса ответа.
     *
     * @param int $status Код статуса ответа
     * @param bool $default_body Использовать ли стандартное тело ответа
     *
     * @throws \Lib_Exception_InvalidArgument
     *
     * @return HttpResponse $this
     */
    public function setStatus($status, $default_body = false)
    {
        if (false === is_int($status) || false === isset(self::$_status_codes[$status])) {
            throw new \Lib_Exception_InvalidArgument('Invalid http-response status code');
        }
        $this->setHeader($_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0', ' ' . $status . ' ' . self::$_status_codes[$status]);
        $this->_status = $status;

        if ($default_body === true) {
            $this->setDefaultBody();
        }

        return $this;
    }

    /**
     * Получение статуса ответа.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * Установка даты модификации
     *
     * @param int $time
     *
     * @throws \Lib_Exception_InvalidArgument_Type
     *
     * @return HttpResponse $this
     */
    public function setLastModified($time)
    {
        if (false === is_int($time)) {
            throw new \Lib_Exception_InvalidArgument_Type($time, 'int');
        }
        $this->_last_modified = $time;

        return $this;
    }

    /**
     * Установка даты модификации если она позже, чем так что уже установлена.
     *
     * @param int|null $time
     *
     * @throws \Lib_Exception_InvalidArgument_Type
     *
     * @return HttpResponse $this
     */
    public function setLatestLastModified($time)
    {
        if (null === $time) {
            return $this;
        }

        if (false === is_int($time)) {
            throw new \Lib_Exception_InvalidArgument_Type($time, 'int');
        }

        if (null === $this->_last_modified || $this->_last_modified < $time) {
            $this->_last_modified = $time;
        }

        return $this;
    }

    /**
     * Получение даты модификации
     *
     * @return int
     */
    public function getLastModified()
    {
        return $this->_last_modified;
    }

    /**
     * Установка ETag.
     *
     * @param string $etag Значение ETag
     *
     * @throws \Lib_Exception_InvalidArgument_Type
     *
     * @return HttpResponse $this
     */
    public function setEtag($etag)
    {
        if (false === is_string($etag)) {
            throw new \Lib_Exception_InvalidArgument_Type($etag, 'string');
        }
        $this->_etag = $etag;
        $this->setHeader('Etag', $this->_etag);

        return $this;
    }

    /**
     * Примешивание ETag, для создания общего уникального ETag-а.
     *
     * @param $etag
     *
     * @return HttpResponse
     */
    public function mixEtag($etag)
    {
        if (null === $this->_etag) {
            $this->_etag = $etag;
        } else {
            $this->_etag = sha1($this->_etag . '|' . $etag);
        }
        $this->setHeader('Etag', $this->_etag);

        return $this;
    }

    /**
     * Возвращает текущий ETag.
     *
     * @return string
     */
    public function getEtag()
    {
        return $this->_etag;
    }

    /**
     * Проверка на соответсвтие ETag и Last-Modified с HTTP_IF_NONE_MATCH и HTTP_IF_MODIFIED_SINCE.
     * В случае прохождение проверки в $response ставится код 304 и возвращается true, иначе - false.
     *
     * @param \System\HttpRequest $request Объект HTTP-запроса
     *
     * @return bool
     */
    public function matchCacheHeaders(HttpRequest $request)
    {
        if (null === $this->_etag && null === $this->_last_modified) {
            return false;
        }

        $match = $request->server['HTTP_IF_NONE_MATCH']->value();

        if ($match && (null === $this->_etag || $match != $this->_etag)) {
            return false;
        }

        $since = $request->server['HTTP_IF_MODIFIED_SINCE']->value();

        if ($since) {
            if (null === $this->_last_modified) {
                return false;
            }

            $now = time();

            if ($this->_last_modified > $now) {
                $this->_last_modified = $now;
            }

            if (strtotime($since) < $this->_last_modified) {
                return false;
            }
        }

        if (empty($match) && empty($since)) {
            return false;
        }

        $this->setStatus(304);

        return true;
    }

    /**
     * Устанавливает тело ответа по умолчанию, в соответствии со статусом ответа
     *
     * @return HttpResponse $this
     */
    public function setDefaultBody()
    {
        if (null !== ($body = self::_getDefaultBody($this->_status))) {
            $this->setContentType('text/html');
            $this->_body = $body;
        }

        return $this;
    }

    /**
     * Преобразование объекта HTTP-ответа в строку с установкой заголовков.
     *
     * Возвращается тело ответа, заголовки устанавливаются посредством header().
     * Также устанавливаются cookies через setcookie().
     * Тело ответа перекодируется во внешнюю кодировку если она не совпадает со внутренней.
     * Отладка в error_log, включается GET параметром http_debug.
     *
     * Для вывода http-ответа можно использовать echo:
     *
     * @code{.php}
     * $response = new HttpResponse( $body );
     * echo $response;
     * @endcode
     *
     * @return string
     */
    public function __toString()
    {
        // Установка заголовков
        foreach ($this->_headers as $name => $value) {
            header($name . ': ' . strval($value));
        }

        // Установка cookies
        foreach ($this->_cookies as $name => $cookie) {
            setcookie($name, $cookie['value'], $cookie['expire'], $cookie['path'], $cookie['domain'], $cookie['secure']);
        }

        // Вывод тела ответа
        if ($this->_status == 304) {
            return '';
        }

        if ($this->_is_encode) {
            return iconv(self::$_internal_encoding, $this->_external_encoding, strval($this->_body));
        } else {
            return strval($this->_body);
        }
    }

    /**
     * Устанавливает мета-заголовки в объекте ответа.
     * Из $title списки списки скриптов и стилей, а также опциональные
     * ключ кэширования и время жизни кэша записываются в мета-заголовки $response.
     *
     * @param \Lib_Html_Titles $title Объект заголовка страницы
     * @param string|null $key Ключ кэша (опционально)
     * @param int $lifetime Время жизни кэша (по умолчанию - 0)
     *
     * @throws \Lib_Exception_InvalidArgument_Type
     * @throws \Lib_Exception_InvalidArgument
     *
     * @return HttpResponse $this
     */
    public function setMetaHeaders(\Lib_Html_Titles $title, $key = null, $lifetime = 0)
    {
        if (null !== $key) {
            if (false === is_string($key)) {
                throw new \Lib_Exception_InvalidArgument_Type($key, 'string');
            }
            $this->setHeader(self::X_META_CACHE_KEY, $key);
        }

        if (false === is_int($lifetime)) {
            throw new \Lib_Exception_InvalidArgument_Type($lifetime, 'int');
        }

        if ($lifetime < 0) {
            throw new \Lib_Exception_InvalidArgument('Lifetime must be greater or equal zero.');
        }
        $this->setHeader(self::X_META_CACHE_LIFETIME, strval($lifetime));

        if (count($title->Styles)) {
            $styles = [];

            foreach ($title->Styles as $style) {
                $styles[] = $style['href'];
            }
            $this->setHeader(self::X_META_STYLES, serialize($styles));
        }

        if (count($title->Scripts)) {
            $scripts = [];

            foreach ($title->Scripts as $script) {
                $scripts[] = $script['src'];
            }
            $this->setHeader(self::X_META_SCRIPTS, serialize($scripts));
        }

        return $this;
    }

    /**
     * Получение массива мета-заголовков ответа.
     *
     * @return array Массив мета-заголовков ответа
     */
    public function getMetaHeaders()
    {
        $meta = [];

        foreach ($this->_headers as $name => $value) {
            if (substr($name, 0, strlen(\System\HttpResponse::X_META_PREFIX)) != \System\HttpResponse::X_META_PREFIX) {
                continue;
            }

            if ($name == \System\HttpResponse::X_META_STYLES || $name == \System\HttpResponse::X_META_SCRIPTS) {
                $meta[$name] = unserialize($value);
            } else {
                $meta[$name] = $value;
            }
        }

        return $meta;
    }

    /**
     * Обрабатывает мета-заголовки, добавляя списки стилей и скриптов.
     *
     * @static
     *
     * @param array $meta Массив мета-заголовков
     * @param \Lib_Html_Titles $title Объект заголовка страницы
     */
    public static function ProcessMetaHeaders(array $meta, \Lib_Html_Titles $title)
    {
        if (isset($meta[self::X_META_STYLES])) {
            $title->addStyles($meta[self::X_META_STYLES]);
        }

        if (isset($meta[self::X_META_SCRIPTS])) {
            $title->addScripts($meta[self::X_META_SCRIPTS]);
        }
    }

    /**
     * Получение тела ответа по умолчанию.
     * Если стандартное тело ответа отсутствует - вернет null.
     *
     * @static
     *
     * @param int $status Код статуса ответа
     */
    private static function _getDefaultBody($status)
    {
        if (is_file(sprintf(ENGINE_PATH . 'static/%s.html', $status))) {
            return file_get_contents(sprintf(ENGINE_PATH . 'static/%s.html', $status));
        }

        return null;
    }
}
