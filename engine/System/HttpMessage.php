<?php

namespace System;

/**
 * Базовый класс Http-сообщения
 *
 * @package System
 */
class HttpMessage
{
    public const DEFAULT_INTERNAL_ENCODING = 'utf-8';
    public const DEFAULT_EXTERNAL_ENCODING = 'utf-8';

    /**
     * Кодировка, в которой работает приложение
     *
     * @var string
     */
    protected static $_internal_encoding = self::DEFAULT_INTERNAL_ENCODING;

    /**
     * Кодировка, в которой производится выдача сообщения
     *
     * @var string
     */
    protected $_external_encoding = self::DEFAULT_EXTERNAL_ENCODING;

    /**
     * Требуется ли перекодировка сообщения
     *
     * @var bool
     */
    protected $_is_encode = false;

    /** @var string Тело */
    protected $_body;

    /** @var array Заголовки */
    protected $_headers = [];

    /** @var array Cookies */
    protected $_cookies = [];

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

    public static function SetInternalEncoding($encoding)
    {
        self::$_internal_encoding = $encoding;
    }

    /**
     * Получение внешней кодировки, в которой передаются аргументы.
     *
     * @return string
     */
    public function getExternalEncoding()
    {
        return $this->_external_encoding;
    }

    /**
     * Установка кодировки для получения аргментов.
     * Если внутренняя и внешняя кодировки не совпадают - будет установлен флаг перекодировки
     * и аргументы будут переводится из внутренней кодировки во внешнюю, если параметр
     * $encode равен true.
     *
     * @param string $encoding Кодировка
     * @param bool $encode Требуется ли перекодировка, или контент поставляется уже в нужной кодировке
     *
     * @return HttpMessage $this
     */
    public function setExternalEncoding($encoding, $encode = true)
    {
        $this->_external_encoding = $encoding;

        if (true === $encode && strcasecmp(self::$_internal_encoding, $this->_external_encoding) == 0) {
            $this->_is_encode = false;
        } else {
            $this->_is_encode = true;
        }

        return $this;
    }

    /**
     * Получение флага перекодировки.
     *
     * @return bool
     */
    public function isEncode()
    {
        return $this->_is_encode;
    }

    /**
     * Установка всех заголовков сразу.
     * Перезаписывает уже установленные заголовки.
     *
     * @param array $headers Массив заголовков
     *
     * @return HttpResponse $this
     */
    public function setHeaders(array $headers)
    {
        $this->_headers = $headers;

        return $this;
    }

    /**
     * Получение заголовков с приведением каждого к строке.
     *
     * @return array
     */
    public function getHeaders()
    {
        return array_map('strval', $this->_headers);
    }

    /**
     * Установка заголовка
     *
     * @param $name
     * @param $value
     *
     * @return HttpMessage $this
     */
    public function setHeader($name, $value)
    {
        $this->_headers[$name] = $value;

        return $this;
    }

    /**
     * Получение заголовка с приведением к строке
     *
     * @param string $name
     *
     * @return string|null
     */
    public function getHeader($name)
    {
        return isset($this->_headers[$name]) ? strval($this->_headers[$name]) : null;
    }

    /**
     * Устанавливает тело сообщения.
     *
     * @param string $body Тело сообщения
     *
     * @return HttpMessage $this
     *
     * @throws \Lib_Exception_InvalidArgument_Type
     */
    public function setBody($body)
    {
        if (false === is_string($body)) {
            throw new \Lib_Exception_InvalidArgument_Type($body, 'string');
        }

        $this->_body = $body;

        return $this;
    }

    /**
     * Возвращает текущее тело сообщения.
     *
     * @return string|null
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * Устанавливает cookie.
     *
     * @param string $name Имя
     * @param string|null $value Значение
     * @param int|null $expire Срок действия
     * @param string|null $path Путь
     * @param string|null $domain Домен
     * @param bool|null $secure Защищенное cookie для HTTPS
     *
     * @throws \Lib_Exception_InvalidArgument_Type
     */
    public function setCookie($name, $value, $expire = null, $path = '/', $domain = null, $secure = false)
    {
        if (false === is_string($name)) {
            throw new \Lib_Exception_InvalidArgument_Type($name, 'string');
        }

        if (null !== $value && false === is_string($value)) {
            throw new \Lib_Exception_InvalidArgument_Type($value, 'string');
        }

        if (null !== $expire && false === is_int($expire)) {
            throw new \Lib_Exception_InvalidArgument_Type($expire, 'string');
        }

        if (null !== $path && false === is_string($path)) {
            throw new \Lib_Exception_InvalidArgument_Type($path, 'string');
        }

        if (null !== $domain && false === is_string($domain)) {
            throw new \Lib_Exception_InvalidArgument_Type($domain, 'string');
        }
        $this->_cookies[$name] = [
            'name' => $name,
            'value' => $value,
            'expire' => $expire,
            'path' => $path,
            'domain' => $domain,
            'secure' => ($secure === true),
        ];
    }

    /**
     * Удаляет cookie.
     *
     * @param $name
     */
    public function unsetCookie($name)
    {
        unset($this->_cookies[$name]);
    }

    /**
     * Возвращает значение установленной cookie.
     * Если заданная cookie не установлена - вернет null
     *
     * @param $name
     *
     * @return string | null
     */
    public function getCookie($name)
    {
        if (isset($this->_cookies[$name])) {
            return $this->_cookies[$name];
        }

        return null;
    }

    /**
     * Возвращает массив установленных cookie.
     * Формат выдачи:
     *
     * @code{.php}
     *     array(
     *         '<name>' => array(
     *             'name' => '<имя>',
     *             'value' => '<значение>',
     *             'expire' => '<срок действия>',
     *             'path' => '<путь>',
     *             'domain' => '<домен>',
     *             'secure' => '<защита https>',
     *         ),
     *     );
     * @endcode;
     *
     * @return array
     */
    public function getCookies()
    {
        return $this->_cookies;
    }

    /**
     * Объединяет cookies двух http-ответов.
     *
     * @param HttpMessage $message
     */
    public function mergeCookies(HttpMessage $message)
    {
        $this->_cookies = array_merge($this->_cookies, $message->getCookies());
    }
}
