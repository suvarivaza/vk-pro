<?php

use Lib\Cassa\Query\Expr\Factory as Cql;

/**
 * Class Lib_ORM_CqlObject.
 */
abstract class Lib_ORM_CqlObject
{
    public const TYPE_ASCII = 0x00000001;
    public const TYPE_BIGINT = 0x00000002;
    public const TYPE_BLOB = 0x00000004;
    public const TYPE_BOOLEAN = 0x00000008;
    public const TYPE_COUNTER = 0x00000010;
    public const TYPE_DECIMAL = 0x00000020;
    public const TYPE_DOUBLE = 0x00000040;
    public const TYPE_FLOAT = 0x00000080;
    public const TYPE_INET = 0x00000100;
    public const TYPE_INT = 0x00000200;
    public const TYPE_TEXT = 0x00000400;
    public const TYPE_TIMESTAMP = 0x00000800;
    public const TYPE_TIMEUUID = 0x00001000;
    public const TYPE_UUID = 0x00002000;
    public const TYPE_VARCHAR = 0x00004000;
    public const TYPE_VARINT = 0x00008000;

    public const FLAG_PK = 0x01000000;

    public const MASK_TYPE = 0x00ffffff;
    public const MASK_FLAG = 0xff000000;

    /** @var array */
    private $_data = [];

    public function __construct()
    {
        $pk = static::getPkSpec();

        if (empty($pk)) {
            throw new \Lib_Exception_Logic(
                'CqlObject must have primary key(s) defined.'
            );
        }
    }

    /**
     * @return array
     */
    public static function getPropSpec()
    {
        /* We can not make static methods abstract. */
    }

    /**
     * @return array
     */
    public static function getPkSpec()
    {
        $pk = [];

        foreach (static::getPropSpec() as $prop => $spec) {
            if ($spec & self::FLAG_PK) {
                $pk[$prop] = $spec;
            }
        }

        return $pk;
    }

    /**
     * @param string|null $val
     * @param int $type
     *
     * @throws Lib_Exception_Logic
     *
     * @return \Lib\Cassa\Query\Expr\Constant\Base
     */
    public static function getCqlConst($val, $type)
    {
        if (is_null($val)) {
            return Cql::null();
        } elseif (is_array($val)) {
            throw new \Lib_Exception_Logic(
                'Vectors are not supported here.'
            );
        }

        switch ($type & self::MASK_TYPE) {
            case self::TYPE_BLOB:
                return Cql::blob($val);

            case self::TYPE_BOOLEAN:
                return Cql::bool($val);

            case self::TYPE_DECIMAL:
            case self::TYPE_DOUBLE:
            case self::TYPE_FLOAT:
                return Cql::float($val);

            case self::TYPE_BIGINT:
            case self::TYPE_COUNTER:
            case self::TYPE_INT:
            case self::TYPE_VARINT:
                return Cql::int($val);

            case self::TYPE_ASCII:
            case self::TYPE_INET:
            case self::TYPE_TEXT:
            case self::TYPE_VARCHAR:
                return Cql::str($val);

            case self::TYPE_TIMEUUID:
            case self::TYPE_UUID:
                return Cql::uuid($val);

            case self::TYPE_TIMESTAMP:
                return Cql::int($val * 1000);

            default:
                throw new \Lib_Exception_Logic(
                    'Unknown datatype: ' . $type
                );
        }
    }

    /**
     * @return int
     */
    abstract public function getHash();

    public function __get($name)
    {
        $props = static::getPropSpec();

        if (!array_key_exists($name, $props)) {
            throw new \Lib_Exception_UnknownProperty(
                $name,
                get_class($this)
            );
        }

        return isset($this->_data[$name]) ? $this->_data[$name] : null;
    }

    public function __set($name, $value)
    {
        $props = static::getPropSpec();

        if (!array_key_exists($name, $props)) {
            throw new \Lib_Exception_UnknownProperty(
                $name,
                get_class($this)
            );
        }

        if (is_null($value)) {
            if ($props[$name] & self::FLAG_PK) {
                throw new \Lib_Exception_InvalidArgument(
                    'Primary key can not be null.'
                );
            }

            $this->_data[$name] = null;

            return;
        }

        switch ($props[$name] & self::MASK_TYPE) {
            case self::TYPE_BOOLEAN:
                if (!is_bool($value)) {
                    throw new \Lib_Exception_InvalidArgument(
                        '$value must be boolean.'
                    );
                }

                break;

            case self::TYPE_DECIMAL:
            case self::TYPE_DOUBLE:
            case self::TYPE_FLOAT:
                if (!is_float($value)) {
                    throw new \Lib_Exception_InvalidArgument(
                        '$value must be float.'
                    );
                }

                break;

            case self::TYPE_BIGINT:
            case self::TYPE_COUNTER:
            case self::TYPE_INT:
            case self::TYPE_VARINT:
                if (!is_int($value)) {
                    throw new \Lib_Exception_InvalidArgument(
                        '$value must be integer.'
                    );
                }

                break;

            case self::TYPE_TIMEUUID:
            case self::TYPE_UUID:
                if (!\Lib_Uuid::isValid($value)) {
                    throw new \Lib_Exception_InvalidArgument(
                        "'$value' is not a valid uuid."
                    );
                }

                // no break
            case self::TYPE_BLOB:
            case self::TYPE_ASCII:
            case self::TYPE_INET:
            case self::TYPE_TEXT:
            case self::TYPE_VARCHAR:
                if (!is_string($value)) {
                    throw new \Lib_Exception_InvalidArgument(
                        '$value must be string.'
                    );
                }
                break;

            case self::TYPE_TIMESTAMP:
                if (is_int($value) || is_double($value)) {
                    $value = (int) $value;
                } else {
                    throw new \Lib_Exception_InvalidArgument(
                        '$value must be integer or float.'
                    );
                }

                break;
        }

        $this->_data[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }
}
