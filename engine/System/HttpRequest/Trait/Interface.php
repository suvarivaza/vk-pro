<?php

namespace System;

/**
 * Интерфейс трейта запроса
 *
 * @package System
 */
interface HttpRequest_Trait_Interface extends \ArrayAccess
{
    public const URL_ENFORCE_HTTP_SCHEME = 1;
    public const URL_REQUIRED_SCHEME = 2;
    public const URL_REQUIRED_HOST = 4;

    public function __construct($data = null);

    public function value($flags = null);

    public function string($default = '', $flags = null);

    public function int($default = false, $flags = HttpRequest::SIGNED_NUM);

    public function dec($default = false, $flags = HttpRequest::SIGNED_NUM);

    public function alpha($default = false);

    public function alphaNum($default = false);

    public function email($default = false);

    public function url($default = null, $encode = true);

    public function url2($default = '');

    /**
     * Возвращает текущее значение, если оно является валидным URN; иначе возвращает $default.
     *
     * @param string $default
     *
     * @return mixed
     */
    public function urn($default = '');

    public function url3($flags = null);

    public function bool($default = false);

    public function enum($default = null, $set = null, $flags = null);

    public function dateTime($default = null);

    public function asArray($default = null, $flags = null);

    public function isEmpty();
}
