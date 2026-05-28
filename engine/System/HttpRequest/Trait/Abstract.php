<?php

namespace System;

/**
 * Абстрактный класс трейта запроса.
 *
 * @package System
 */
abstract class HttpRequest_Trait_Abstract implements HttpRequest_Trait_Interface, \Countable
{
    /**
     * Данные трейта
     *
     * @var array|null
     */
    protected $_data = null;

    /**
     * Кодировка, в которой производится выдача сообщения
     *
     * @var string
     */
    protected $_external_encoding = HttpMessage::DEFAULT_EXTERNAL_ENCODING;

    /**
     * Требуется ли перекодировка сообщения
     *
     * @var bool
     */
    protected $_is_encode = false;

    public const URN_REGEXP = '/^urn:[a-z0-9][a-z0-9-]{1,31}:([a-z0-9()+,\-\.:=@;$_!*\']|%(0[1-9a-f]|[1-9a-f][0-9a-f]))+$/i';

    /**
     * Конструктор
     *
     * @param array $data Данные трейта
     *
     * @return \System\HttpRequest_Trait_Abstract
     */
    public function __construct($data = null)
    {
        $this->_data = $data;
    }

    protected function _isValidUrl($url)
    {
        $parts = parse_url($url);

        return !(
            empty($parts) || empty($parts['host']) || empty($parts['scheme'])
        );
    }

    protected function _isValidUrn($urn)
    {
        return (bool) preg_match(self::URN_REGEXP, $urn);
    }

    /**
     * Установка кодировки для получения аргментов.
     * Если внутренняя и внешняя кодировки не совпадают - будет установлен флаг перекодировки
     * и аргументы будут переводится из внутренней кодировки во внешнюю, если параметр
     * $encode равен true.
     *
     * @param string $encoding Кодировка
     * @param bool $encode Требуется ли перекодировка, или аргументы поставляется уже в нужной кодировке
     *
     * @return \System\HttpRequest_Trait_Abstract
     */
    public function setExternalEncoding($encoding, $encode)
    {
        $this->_external_encoding = $encoding;
        $this->_is_encode = $encode;

        return $this;
    }

    /**
     * Пострение строки запроса по параметрам трейта
     *
     * @param array $params Имена аргументов
     * @param int $include Что необходимо сделать с аргументами, имена которых указаны в $params
     *
     * @return string
     */
    public function buildQueryString($params = null, $include = HttpRequest::INCLUDE_VARS)
    {
        $data = $this->asArray();

        // Если параметров не указано, то выдаем строку запроса на весь запрос
        if ($params === null) {
            return http_build_query($data, '', '&');
        }

        if (false === is_array($params)) {
            return '';
        }

        array_walk($data, function (&$value, $key) use ($include, $params) {
            if ($include == HttpRequest::EXCLUDE_VARS && in_array($key, $params)) {
                $value = null;
            } elseif ($include == HttpRequest::INCLUDE_VARS && !in_array($key, $params)) {
                $value = null;
            }
        });

        $data = array_filter($data, function ($value) {
            return !is_null($value);
        });

        array_walk_recursive($data, 'trim');

        return http_build_query($data, '', '&');
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     *
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->_data);
    }

    /**
     * Деструктор
     */
    public function __destruct()
    {
        $this->_data = null;
    }
}
