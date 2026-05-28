<?php

/**
 * Class Lib_Json.
 */
class Lib_Json
{
    /**
     * Encodes $value to JSON string.
     *
     * @see \JsonSerializable
     *
     * @param mixed $value
     * @param bool $noEscaping
     * @param bool $beautify
     *
     * @return string
     */
    public static function encode($value, $noEscaping = false, $beautify = false)
    {
        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            $flags = 0;
            $flags |= $noEscaping ? JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE : 0;
            $flags |= $beautify ? JSON_PRETTY_PRINT : 0;

            return json_encode($value, $flags);
        } else {
            $result = json_encode($value);

            if ($noEscaping) {
                $result = self::UnescapeSlashes($result);
                $result = self::UnescapeUnicode($result);
            }

            if ($beautify) {
                $result = self::Beautify($result);
            }

            return $result;
        }
    }

    /**
     * Decodes JSON string $value to native php value.
     *
     * @param string $value
     * @param bool $object
     *
     * @throws \Lib_Exception_Runtime
     *
     * @return mixed
     */
    public static function decode($value, $object = false)
    {
        $result = json_decode($value, !$object);

        switch ($code = json_last_error()) {
            case JSON_ERROR_NONE:
                break;

            case JSON_ERROR_DEPTH:
                throw new \Lib_Exception_Runtime(
                    'The maximum stack depth has been exceeded.'
                );

            case JSON_ERROR_STATE_MISMATCH:
                throw new \Lib_Exception_Runtime(
                    'Invalid or malformed JSON.'
                );

            case JSON_ERROR_CTRL_CHAR:
                throw new \Lib_Exception_Runtime(
                    'Control character error, possibly incorrectly encoded.'
                );

            case JSON_ERROR_SYNTAX:
                throw new \Lib_Exception_Runtime(
                    'Syntax error.'
                );

            case JSON_ERROR_UTF8:
                throw new \Lib_Exception_Runtime(
                    'Malformed UTF-8 characters, possibly incorrectly encoded.'
                );

            default:
                throw new \Lib_Exception_Runtime(
                    'Unknown error occurred while decoding json. Error code: ' . $code . '.'
                );
        }

        return $result;
    }

    /**
     * Форматирует json
     * расставляет отступы
     *
     * @static
     *
     * @param string $json JSON
     * @param int $options Опции
     *
     * @return string
     */
    public static function Beautify($json, $options = 0)
    {
        $indentStr = $options['indentStr'] ?? "\t";
        $newLine = $options['newLine'] ?? "\n";

        $json = str_replace('":true', '": true', $json);
        $json = str_replace('":false', '": false', $json);

        $result = '';
        $pos = 0;
        $strLen = strlen($json);
        $prevChar = '';
        $outOfQuotes = true;

        for ($i = 0; $i <= $strLen; $i++) {
            $char = substr($json, $i, 1);

            if ($char == '"' && $prevChar != '\\') {
                $outOfQuotes = !$outOfQuotes;
            } elseif (($char == '}' || $char == ']') && $outOfQuotes) {
                $result .= $newLine;
                $pos--;

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            if ($prevChar == ':' && (($char == '"' && false === $outOfQuotes)
                    || ($char == '[' || $char == '{' || stristr('0123456789', $char)) && true === $outOfQuotes)
            ) {
                $result .= ' ';
            }

            $result .= $char;

            if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
                $result .= $newLine;

                if ($char == '{' || $char == '[') {
                    $pos++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            $prevChar = $char;
        }

        return $result;
    }

    /**
     * Убирает экранировние слешей
     *
     * @param string $json JSON
     *
     * @return mixed
     */
    public static function UnescapeSlashes($json)
    {
        return str_replace('\/', '/', $json);
    }

    /**
     * Преобразует экранированный UTF в неэкранированный
     *
     * @static
     *
     * @param string $json JSON
     *
     * @return string
     */
    public static function UnescapeUnicode($json)
    {
        $json = preg_replace_callback('/\\\u(\w\w\w\w)/', function ($matches) {
            return '&#' . hexdec($matches[1]) . ';';
        }, $json);

        $result = mb_decode_numericentity($json, [0x0, 0x2FFFF, 0, 0xFFFF], 'utf-8');

        return $result;
    }
}
