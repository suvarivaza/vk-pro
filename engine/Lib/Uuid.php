<?php

final class Lib_Uuid
{
    public function __construct()
    {
        // no instances.
        throw new \LogicException(__CLASS__ . ' can not be instantiated.');
    }

    protected static function _normalizeUUID($uuid)
    {
        if (!is_string($uuid)) {
            throw new \Lib_Exception_InvalidArgument('$uuid must be a string.');
        }

        if (uuid_is_valid($uuid)) {
            return $uuid;
        } elseif (strlen($uuid) == 32 && ctype_xdigit($uuid)) { // hex uuid
            return substr($uuid, 0, 8) . '-'
            . substr($uuid, 8, 4) . '-'
            . substr($uuid, 12, 4) . '-'
            . substr($uuid, 16, 4) . '-'
            . substr($uuid, 20, 12);
        } elseif (strlen($uuid) == 16) { // binary uuid
            return uuid_unparse($uuid);
        } else {
            throw new \Lib_Exception_InvalidArgument('$uuid must be a valid UUID.');
        }
    }

    public static function toBinary($uuid)
    {
        return uuid_parse(self::_normalizeUUID($uuid));
    }

    public static function toHex($uuid)
    {
        return str_replace('-', '', self::_normalizeUUID($uuid));
    }

    public static function toString($uuid)
    {
        return self::_normalizeUUID($uuid);
    }

    /**
     * Возвращает следующий уникальный индентификатор.
     *
     * Возвращаемый идентификатор является UUID 1 или 4 версии
     * (в зависимости от доступности источника случайных данных).
     *
     * @link http://en.wikipedia.org/wiki/UUID#Variants_and_versions
     *
     * @return string
     */
    public static function getNext()
    {
        return uuid_create(UUID_TYPE_DEFAULT);
    }

    /**
     * @return string
     */
    public static function getNextTimeUuid()
    {
        return uuid_create(UUID_TYPE_TIME);
    }

    /**
     * @param int $time
     *
     * @return string
     *
     * @throws Lib_Exception_InvalidArgument
     */
    public static function getTimeUuid($time)
    {
        if (!is_int($time)) {
            throw new \Lib_Exception_InvalidArgument('$uuid must be an integer.');
        }

        return uuid_create_my(UUID_TYPE_TIME, $time);
    }

    /**
     * Проверяет, является ли $uuid корректным идентификатором.
     *
     * @param string $uuid
     *
     * @return bool
     */
    public static function isValid($uuid)
    {
        try {
            self::_normalizeUUID($uuid);
        } catch (\Lib_Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Проверяет, является ли $uuid корректным идентификатором RFC 4122 версии 1.
     *
     * @param string $uuid
     *
     * @return bool
     */
    public static function isTimeUuid($uuid)
    {
        try {
            $data = unpack('C*', self::toBinary($uuid));
        } catch (\Lib_Exception_InvalidArgument $e) {
            return false;
        }

        $variant = ($data[8 + 1] & 0xc0) >> 6;
        $version = ($data[6 + 1] & 0xf0) >> 4;

        return $variant == 2 && $version == 1;
    }

    /**
     * @param string $uuid
     *
     * @throws Lib_Exception_InvalidArgument
     *
     * @return float
     */
    public static function getTimeFromUuid($uuid)
    {
        if (!self::isTimeUuid($uuid)) {
            throw new \Lib_Exception_InvalidArgument(
                'Supplied uuid must be a valid RFC 4122 version 1 uuid.'
            );
        }

        $data = unpack('Nlow/nmid/nhi', self::toBinary($uuid));
        $time = ($data['hi'] & 0x0fff) << 48 | $data['mid'] << 32 | $data['low'];

        return  ($time - 0x01b21dd213814000) * 100 / 1e9;
    }
}

/*
 * Dependency break from pecl uuid extension.
 */

// @codeCoverageIgnoreStart
if (!extension_loaded('uuid')) {
    define('UUID_VARIANT_NCS', 0);
    define('UUID_VARIANT_DCE', 0);
    define('UUID_VARIANT_MICROSOFT', 0);
    define('UUID_VARIANT_OTHER', 0);

    define('UUID_TYPE_DEFAULT', 0);
    define('UUID_TYPE_TIME', 1);
    define('UUID_TYPE_DCE', 2);
    define('UUID_TYPE_NAME', 3);
    define('UUID_TYPE_RANDOM', 4);
    define('UUID_TYPE_NULL', 0);
    define('UUID_TYPE_INVALID', 0);

    function uuid_is_valid($uuid)
    {
        if (strlen($uuid) != 36) {
            return false;
        }

        for ($i = 0; $i < 36; $i++) {
            if ($i == 8 || $i == 13 || $i == 18 || $i == 23) {
                if ($uuid[$i] == '-') {
                    continue;
                } else {
                    return false;
                }
            } elseif (!ctype_xdigit($uuid[$i])) {
                return false;
            }
        }

        return true;
    }

    function uuid_unparse($string)
    {
        if (strlen($string) != 16) {
            return false;
        }

        return implode('-', unpack('H8a/H4b/H4c/H4d/H12e', $string));
    }

    function uuid_parse($string)
    {
        if (!uuid_is_valid($string)) {
            return false;
        }

        return pack('H*', str_replace('-', '', $string));
    }

    function uuid_create($type = UUID_TYPE_DEFAULT, $time = null)
    {
        switch ($type) {
            case UUID_TYPE_TIME:
                $uuid = uuid_get_random_bytes(16);

                if (is_int($time)) {
                    list($micro) = explode(' ', microtime());

                    $time = $time + (float) $micro;
                } else {
                    $time = microtime(true);
                }

                $time = (int) ($time * 1e9 / 100 + 0x01b21dd213814000);

                // And now to a 64-bit binary representation
                $time = base_convert($time, 10, 16);
                $time = pack('H*', str_pad($time, 16, '0', STR_PAD_LEFT));

                foreach ([
                    0 => 4,
                    1 => 5,
                    2 => 6,
                    3 => 7,
                    4 => 2,
                    5 => 3,
                    6 => 0,
                    7 => 1,
                ] as $k => $v) {
                    $uuid[$k] = $time[$v];
                }

                // version
                $uuid[6] = chr(ord($uuid[6]) & 0x0f | 0x10);

                // variant
                $uuid[8] = chr(ord($uuid[8]) & 0x3f | 0x80);

                break;

            case UUID_TYPE_RANDOM:
            case UUID_TYPE_DEFAULT:
                $uuid = uuid_get_random_bytes(16);

                // version
                $uuid[6] = chr(ord($uuid[6]) & 0x0f | 0x40);

                // variant
                $uuid[8] = chr(ord($uuid[8]) & 0x3f | 0x80);

                break;
        }

        return uuid_unparse($uuid);
    }
}

function uuid_get_random_bytes($count)
{
    if ($data = file_get_contents('/dev/urandom', null, null, null, $count)) {
        // system source of random data is available
    } else {
        // using mt_rand fallback
        for ($data = '', $i = 0; $i < $count; $i++) {
            $data .= chr(mt_rand(0, 255));
        }

        user_error('Using mt_rand() fallback for uuid generation.', E_USER_ERROR);
    }

    return $data;
}

function uuid_create_my($type = UUID_TYPE_DEFAULT, $time = null)
{
    switch ($type) {
        case UUID_TYPE_TIME:
            $uuid = uuid_get_random_bytes(16);

            if (is_int($time)) {
                list($micro) = explode(' ', microtime());

                $time = $time + (float) $micro;
            } else {
                $time = microtime(true);
            }

            $time = (int) ($time * 1e9 / 100 + 0x01b21dd213814000);

            // And now to a 64-bit binary representation
            $time = base_convert($time, 10, 16);
            $time = pack('H*', str_pad($time, 16, '0', STR_PAD_LEFT));

            foreach ([
                0 => 4,
                1 => 5,
                2 => 6,
                3 => 7,
                4 => 2,
                5 => 3,
                6 => 0,
                7 => 1,
            ] as $k => $v) {
                $uuid[$k] = $time[$v];
            }

            // version
            $uuid[6] = chr(ord($uuid[6]) & 0x0f | 0x10);

            // variant
            $uuid[8] = chr(ord($uuid[8]) & 0x3f | 0x80);

            break;

        case UUID_TYPE_RANDOM:
        case UUID_TYPE_DEFAULT:
            $uuid = uuid_get_random_bytes(16);

            // version
            $uuid[6] = chr(ord($uuid[6]) & 0x0f | 0x40);

            // variant
            $uuid[8] = chr(ord($uuid[8]) & 0x3f | 0x80);

            break;
    }

    return uuid_unparse($uuid);
}

// @codeCoverageIgnoreEnd
