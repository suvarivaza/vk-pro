<?php

namespace System;

/**
 * Обычный трейт запроса
 *
 * @package System
 */
class HttpRequest_Trait extends HttpRequest_Trait_Abstract
{
    protected function _encode($val)
    {
        if ($this->_is_encode) {
            mb_convert_variables(
                HttpRequest::GetInternalEncoding(),
                $this->_external_encoding,
                $val
            );
        }

        return $val;
    }

    public function value($flags = null)
    {
        if ($flags === null) {
            return $this->_data;
        }

        if ($this->_is_encode) {
            $buf = $this->_value($this->_data, $flags);
            mb_convert_variables(HttpRequest::GetInternalEncoding(), $this->_external_encoding, $buf);

            return $buf;
        } else {
            return $this->_value($this->_data, $flags);
        }
    }

    private function _value($value, $flags)
    {
        if (is_array($value)) {
            foreach ($value as &$_) {
                $_ = $this->_value($_, $flags);
            }

            return $value;
        }

        if ($flags & HttpRequest::OUT_HTML_CLEAN) {
            $value = strip_tags(html_entity_decode($value));
        }

        if ($flags & HttpRequest::OUT_REMOVE_NON_PRINT_CHARS) {
            $value = preg_replace('@[^[:print:][:space:]]@', '', $value);
        }

        if ($flags & (HttpRequest::OUT_HTML_AREA | HttpRequest::OUT_HTML | HttpRequest::OUT_CHANGE_TAGS)) {
            $value = str_replace('<', '&lt;', $value);
            $value = str_replace('>', '&gt;', $value);
        }

        if ($flags & (HttpRequest::OUT_HTML_AREA | HttpRequest::OUT_HTML | HttpRequest::OUT_CHANGE_QUOTES)) {
            $value = str_replace("'", '&#039;', $value);
            $value = str_replace('"', '&quot;', $value);
        }

        if ($flags & (HttpRequest::OUT_CHANGE_NL)) {
            $value = nl2br($value);
        }

        if ($flags & (HttpRequest::OUT_REMOVE_NL)) {
            $value = preg_replace(["/\n/", "/\r/"], ['', ''], $value);
        }

        if ($flags & (HttpRequest::OUT_HTML_CLEAR_SKYPE)) {
            $value = preg_replace('@begin_of_the_skype_highlighting.*end_of_the_skype_highlighting@', '', $value);
        }

        return trim($value, " \n\r\t ");
    }

    public function string($default = '', $flags = null)
    {
        return $this->_encode(
            $this->_string(
                $this->_data,
                $flags,
                $default
            )
        );
    }

    private function _string($value, $flags, $default)
    {
        if (!is_string($default)) {
            throw new \Lib_Exception_InvalidArgument_Type(
                $default,
                'string'
            );
        }

        if (!is_string($value)) {
            return $default;
        }

        return $this->_value($value, $flags) ?: $default;
    }

    /**
     * @param mixed $default
     * @param int $flags
     *
     * @return array|bool|int|string|null
     */
    public function int($default = false, $flags = HttpRequest::SIGNED_NUM)
    {
        if ($flags === null) {
            return $this->value();
        }

        if (false !== ($_ = $this->_int($this->_data, $flags))) {
            return $_;
        }

        return $default;
    }

    private function _int($value, $flags, $default = false)
    {
        if (false === is_scalar($value)) {
            return $default;
        }

        if ($flags & HttpRequest::OUT_IGNORE_SPACES) {
            $value = str_replace(' ', '', $value);
        }

        if (strpos($value, '-') !== 0 && ctype_digit((string) $value)) {
            return intval($value);
        } elseif (strpos($value, '-') === 0) {
            $value = substr($value, 1);

            if ($flags & HttpRequest::SIGNED_NUM && ctype_digit((string) $value)) {
                return -1 * intval($value);
            }
        }

        return $default;
    }

    /**
     * @param bool|float $default
     * @param int $flags
     *
     * @return array|bool|float|mixed|string|null
     */
    public function dec($default = false, $flags = HttpRequest::SIGNED_NUM)
    {
        if ($flags === null) {
            return $this->value();
        }

        if (false !== ($_ = $this->_dec($this->_data, $flags))) {
            return $_;
        }

        return $default;
    }

    private function _dec($value, $flags, $default = false)
    {
        if ($flags & HttpRequest::OUT_IGNORE_SPACES) {
            $value = str_replace(' ', '', $value);
        }

        $r = localeconv();
        $value = str_replace(',', '.', '' . $value);
        $value = str_replace($r['decimal_point'], '.', '' . $value);

        $number = explode('.', $value, 2);
        $num = $number[0];

        $dec = $number[1] ?? null;
        $dec = ($dec === null || strlen($dec) == 0) ? '0' : $dec;

        if (false !== $this->_int($num, $flags) && false !== $this->_int($dec, HttpRequest::UNSIGNED_NUM)) {
            return (float) "$num.$dec";
        }

        return $default;
    }

    public function alpha($default = false)
    {
        return $this->_alpha($this->_data, $default);
    }

    private function _alpha($value, $default)
    {
        if (ctype_alpha((string) $value)) {
            return $value;
        }

        return $default;
    }

    public function alphaNum($default = false)
    {
        return $this->_alnum($this->_data, $default);
    }

    private function _alnum($value, $default)
    {
        if (ctype_alnum((string) $value)) {
            return $value;
        }

        return $default;
    }

    /**
     * @param mixed $default
     *
     * @return string
     */
    public function email($default = false)
    {
        if ($this->_is_encode) {
            $buf = $this->_email($this->_data, $default);

            if (is_string($buf)) {
                return iconv($this->_external_encoding, HttpRequest::GetInternalEncoding(), $buf);
            } else {
                return $buf;
            }
        } else {
            return $this->_email($this->_data, $default);
        }
    }

    private function _email($value, $default)
    {
        if (\Lib_Text::IsEmail($value)) {
            return $value;
        }

        return $default;
    }

    // @codeCoverageIgnoreStart

    public function url($default = false, $encode = true)
    {
        if ($this->_is_encode === true) {
            $buf = $this->_url($this->_data, $default, $encode);

            return iconv($this->_external_encoding, HttpRequest::GetInternalEncoding(), $buf);
        } else {
            return $this->_url($this->_data, $default, $encode);
        }
    }

    private function _url($value, $default, $encode)
    {
        if (false === is_string($value)) {
            return $default;
        }

        $value = urldecode($value);

        $value = strip_tags($value);
        $trans_tbl = [
            'Ў' => '&iexcl;',
            'ў' => '&cent;',
            'Ј' => '&pound;',
            '¤' => '&curren;',
            'Ґ' => '&yen;',

            '¦' => '&brvbar;',
            '§' => '&sect;',
            'Ё' => '&uml;',
            '©' => '&copy;',
            'Є' => '&ordf;',
            '«' => '&laquo;',

            '¬' => '&not;',
            '­' => '&shy;',
            '®' => '&reg;',
            'Ї' => '&macr;',
            '°' => '&deg;',
            '±' => '&plusmn;',

            'І' => '&sup2;',
            'і' => '&sup3;',
            'ґ' => '&acute;',
            'µ' => '&micro;',
            '¶' => '&para;',
            '·' => '&middot;',

            'ё' => '&cedil;',
            '№' => '&sup1;',
            'є' => '&ordm;',
            '»' => '&raquo;',
            'ј' => '&frac14;',
            'Ѕ' => '&frac12;',

            'ѕ' => '&frac34;',
            'ї' => '&iquest;',

            '"' => '&quot;',
            '\'' => '&#39;',
            '<' => '&lt;',
            '>' => '&gt;',
        ];

        unset($trans_tbl['&']);

        $value = strtr($value, $trans_tbl);

        if (get_magic_quotes_gpc()) {
            $value = stripslashes($value);
        }
        $value = addcslashes($value, "\n\r\\");

        if ($encode) {
            $value = urlencode($value);
        }

        if (strlen($value) > 0) {
            return $value;
        }

        return $default;
    }

    /**
     * @param string $default
     *
     * @return string
     */
    public function url2($default = '')
    {
        $url = $this->_url2(
            $this->_string(
                $this->_data,
                null,
                ''
            ),
            $default
        );

        if ($this->_is_encode) {
            $url = iconv(
                $this->_external_encoding,
                HttpRequest::GetInternalEncoding(),
                $url
            );
        }

        return $url;
    }

    private function _url2($url, $default)
    {
        if (!$this->_isValidUrl($url)) {
            $url = $default;
        }

        return $url;
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
        $urn = $this->_urn(
            $this->_string(
                $this->_data,
                null,
                ''
            ),
            $default
        );

        if ($this->_is_encode) {
            $urn = iconv(
                $this->_external_encoding,
                HttpRequest::GetInternalEncoding(),
                $urn
            );
        }

        return $urn;
    }

    private function _urn($urn, $default)
    {
        if (!$this->_isValidUrn($urn)) {
            $urn = $default;
        }

        return $urn;
    }

    // @codeCoverageIgnoreEnd

    public function bool($default = false)
    {
        if ($this->_data && $this->_data != 'false') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Значение из перечисления
     *
     * @param mixed $default
     * @param array|null $set
     * @param int $flags
     *
     * @return bool|float|int|mixed|string|null
     */
    public function enum($default = null, $set = null, $flags = null)
    {
        if (false === is_array($set)) {
            return $default;
        }

        if ($flags & HttpRequest::INTEGER_NUM) {
            $v = $this->_int($this->_data, $flags, $default);
        } elseif ($flags & HttpRequest::DECIMAL_NUM) {
            $v = $this->_dec($this->_data, $flags, $default);
        } elseif ($flags & HttpRequest::ALPHA) {
            $v = $this->_alpha($this->_data, $default);
        } elseif ($flags & HttpRequest::ALPHA_NUM) {
            $v = $this->_alnum($this->_data, $default);
        } elseif ($flags & HttpRequest::EMAIL) {
            $v = $this->_email($this->_data, $default);
        } elseif ($flags & HttpRequest::URL) {
            $v = $this->_url($this->_data, $default, true);
        } else {
            $v = $this->_value($this->_data, $flags);
        }

        if ($this->_is_encode && is_string($v)) {
            $v = iconv($this->_external_encoding, HttpRequest::GetInternalEncoding(), $v);
        }

        if (in_array($v, $set)) {
            return $v;
        } else {
            return $default;
        }
    }

    public function dateTime($default = null)
    {
        return $this->_datetime($this->_data, $default);
    }

    private function _datetime($value, $default)
    {
        if (!is_scalar($value)) {
            return $default;
        }

        if (is_int($value) && $value > 0) {
            return $value;
        }

        $value = strtotime($value);

        if ($value === false || $value < 0) {
            return $default;
        }

        return $value;
    }

    /**
     * @param null $default
     * @param null $flags
     *
     * @return array
     */
    public function asArray($default = null, $flags = null)
    {
        return $this->_asarray($this->_data, $flags, $default);
    }

    private function _asarray($array, $flags, $default)
    {
        if (is_array($array)) {
            foreach ($array as &$v) {
                if (is_array($v)) {
                    $v = $this->_AsArray($v, $flags, $default);

                    continue;
                }

                if ($flags & HttpRequest::INTEGER_NUM) {
                    $v = $this->_int($v, $flags, 0);
                } elseif ($flags & HttpRequest::DECIMAL_NUM) {
                    $v = $this->_dec($v, $flags, 0);
                } elseif ($flags & HttpRequest::ALPHA) {
                    $v = $this->_alpha($v, '');
                } elseif ($flags & HttpRequest::ALPHA_NUM) {
                    $v = $this->_alnum($v, '');
                } elseif ($flags & HttpRequest::EMAIL) {
                    $v = $this->_email($v, '');
                } elseif ($flags & HttpRequest::URL) {
                    $v = $this->_url($v, '', true);
                } elseif ($flags & HttpRequest::UUID) {
                    $v = $this->_uuid($v, '');
                } elseif ($flags & HttpRequest::DATETIME) {
                    $v = $this->_datetime($v, null);
                } elseif ($flags & HttpRequest::STRING) {
                    $v = $this->_encode(
                        $this->_string(
                            $v,
                            $flags,
                            ''
                        )
                    );
                } else {
                    $v = $this->_value($v, $flags);
                }
            }

            return $array;
        }

        return $default;
    }

    /**
     * @param string $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }

    /**
     * @param string $offset
     *
     * @return HttpRequest_Trait_Abstract
     */
    public function offsetGet($offset)
    {
        if (isset($this->_data[$offset])) {
            $requestObject = new static($this->_data[$offset]);
        } else {
            $requestObject = new HttpRequest_Trait_Empty();
        }

        return $requestObject->setExternalEncoding($this->_external_encoding, $this->_is_encode);
    }

    /**
     * @param string $offset
     * @param mixed $value
     *
     * @throws \Lib_Exception_InvalidArgument
     */
    public function offsetSet($offset, $value)
    {
        if (is_object($value)) {
            throw new \Lib_Exception_InvalidArgument('$value is an object.');
        }

        $this->_data[$offset] = $value;
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);
    }

    public function isEmpty()
    {
        return empty($this->_data);
    }

    public function __toString()
    {
        return $this->string();
    }

    /**
     * @param int $flags
     *
     * @throws \Lib_Exception_InvalidArgument
     *
     * @return bool|\Lib_Url
     */
    public function url3($flags = null)
    {
        if ($flags === null) {
            $flags = self::URL_ENFORCE_HTTP_SCHEME | self::URL_REQUIRED_HOST;
        }

        if ($flags & self::URL_REQUIRED_SCHEME && $flags & self::URL_ENFORCE_HTTP_SCHEME) {
            throw new \Lib_Exception_InvalidArgument(
                'Flags can not contain both URL_REQUIRED_SCHEME and URL_ENFORCE_HTTP_SCHEME.'
            );
        }

        if ($flags & self::URL_ENFORCE_HTTP_SCHEME && !($flags & self::URL_REQUIRED_HOST)) {
            throw new \Lib_Exception_InvalidArgument(
                'Flags can not contain URL_ENFORCE_HTTP_SCHEME without URL_REQUIRED_HOST.'
            );
        }

        if ($this->value() === '') {
            return null;
        }

        if ($this->isEmpty()) {
            return false;
        }

        $url = new \Lib_Url(
            $this->value(),
            'UTF-8'
        );

        if ($url->isMalformed()) {
            return false;
        }

        if ($flags & self::URL_REQUIRED_SCHEME && empty($url->sheme)) {
            return false;
        }

        if ($flags & self::URL_ENFORCE_HTTP_SCHEME && empty($url->sheme)) {
            $url->sheme = 'http';
        }

        if (empty($url->host) && !empty($url->path) && strpos($url->path[0], '.')) {
            $url->host = array_shift($url->path);
        }

        if ($flags & self::URL_REQUIRED_HOST && empty($url->host)) {
            return false;
        }

        return $url;
    }

    /** @var \LibXMLError[] */
    private $_validate_errors = [];

    /**
     * @param array $sheme
     * @param string|string[] $numeric_key_name
     *
     * @return bool
     */
    public function validate(array $sheme, $numeric_key_name = 'id')
    {
        $errors_mode = libxml_use_internal_errors(true);
        $xml = \Lib_Xml::DataToXml($sheme, 'xs:schema', '', 'http://www.w3.org/2001/XMLSchema');
        $data = \Lib_Xml::DataToXml($this->_data, 'request', $numeric_key_name, '', $this->_external_encoding);
        $result = $data->schemaValidateSource($xml->saveXML());

        if (!$result) {
            $this->_validate_errors = libxml_get_errors();
            libxml_clear_errors();
        }
        libxml_use_internal_errors($errors_mode);

        return $result;
    }

    /**
     * @return \LibXMLError[]
     */
    public function validateLastErrors()
    {
        return $this->_validate_errors;
    }
}
