<?php

namespace System;

/**
 * Пустой трейт
 *
 * @package System
 */
class HttpRequest_Trait_Empty extends HttpRequest_Trait_Abstract
{
    /**
     * @param string $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return false;
    }

    /**
     * @param string $offset
     *
     * @return HttpRequest_Trait_Abstract|null
     */
    public function offsetGet($offset)
    {
        return null;
    }

    /**
     * @param string $offset
     * @param mixed $value
     *
     * @return void|null
     */
    public function offsetSet($offset, $value)
    {
        return null;
    }

    /**
     * @param string $offset
     *
     * @return void|null
     */
    public function offsetUnset($offset)
    {
        return null;
    }

    public function uuid($default = false)
    {
        return $default;
    }

    public function value($flags = null)
    {
        return '';
    }

    public function string($default = '', $flags = null)
    {
        return $default;
    }

    public function int($default = false, $flags = HttpRequest::SIGNED_NUM)
    {
        return $default;
    }

    public function dec($default = false, $flags = HttpRequest::SIGNED_NUM)
    {
        return $default;
    }

    public function alpha($default = false)
    {
        return $default;
    }

    public function alphaNum($default = false)
    {
        return $default;
    }

    public function email($default = false)
    {
        return $default;
    }

    public function url($default = null, $encode = true)
    {
        return $default;
    }

    public function url2($default = '')
    {
        return $default;
    }

    /**
     * Возвращает текущее значение, если оно является валидным URN; иначе возвращает $default.
     *
     * @param string $default
     *
     * @return mixed
     */
    public function urn($default = '')
    {
        return $default;
    }

    public function bool($default = false)
    {
        return $default;
    }

    public function enum($default = null, $set = null, $flags = null)
    {
        return $default;
    }

    public function dateTime($default = null)
    {
        return $default;
    }

    public function asArray($default = null, $flags = null)
    {
        return $default;
    }

    public function isEmpty()
    {
        return true;
    }

    public function __toString()
    {
        return '';
    }

    public function url3($flags = null)
    {
        return null;
    }
}
