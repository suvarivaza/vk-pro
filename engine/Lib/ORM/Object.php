<?php

abstract class Lib_ORM_Object
{
    public const TYPE_INT = 1;
    public const TYPE_FLOAT = 2;
    public const TYPE_STRING = 3;
    public const TYPE_BOOL = 4;
    public const TYPE_DATETIME = 5;
    public const TYPE_TIMESTAMP = 6;
    public const TYPE_DATE = 7;

    public const FLAG_NOT_NULL = 16;
    public const FLAG_AUTOINCREMENT = 32;
    public const FLAG_UNSIGNED = 64;
    public const FLAGS_MASK = 15;

    /** @var bool */
    protected static $_Class_Checked = false;

    /** @var array */
    private $_properties;

    /** @var Lib_ORM_Object */
    private $_shadow;

    public function __construct()
    {
        $properties = static::GetPropertiesTypes();

        if (!static::$_Class_Checked) {
            static::$_Class_Checked = true;

            if (!is_array($properties)) {
                throw new \Lib_Exception_Logic_Backtraced('PropertiesTypes must to be an array. Got: ' . gettype($properties));
            }

            foreach ($properties as $field => $type) {
                if (!is_numeric($type)) {
                    throw new \Lib_Exception_Logic_Backtraced('Field: ' . $field . '. Type descriptor must to be a numeric. Got: ' . gettype($type));
                }
            }
        }

        $this->_properties = array_fill_keys(array_keys($properties), null);
    }

    /**
     * @return static
     */
    final public function getShadow()
    {
        return $this->_shadow;
    }

    final public function makeShadow()
    {
        $this->_shadow = clone $this;
    }

    /**
     * @return array|null
     */
    final public function getShadowDifference()
    {
        if (null === $this->_shadow) {
            return null;
        }

        $diff = [];

        foreach (array_keys(static::GetPropertiesTypes()) as $prop) {
            if (call_user_func([$this, '__get'], $prop) !== call_user_func([$this->_shadow, '__get'], $prop)) {
                $diff[] = $prop;
            }
        }

        return $diff;
    }

    /**
     * @return array
     */
    public static function GetPropertiesTypes()
    {
    }

    /**
     * @return array
     */
    final public static function GetPropertiesTypesNoFlags()
    {
        $fields = static::GetPropertiesTypes();

        foreach ($fields as &$type) {
            $type &= self::FLAGS_MASK;
        }

        return $fields;
    }

    final public function checkProperties()
    {
        foreach (static::GetPropertiesTypes() as $prop => $type) {
            if (($type & self::FLAG_AUTOINCREMENT) && $this->_properties[$prop] === null) {
                continue;
            }
            $this->checkProperty($prop);
        }
    }

    /**
     * @param string $prop
     * @param mixed $value
     *
     * @throws \Lib_Exception_Logic_Backtraced
     */
    final public function __set($prop, $value)
    {
        $properties = static::GetPropertiesTypes();

        if (!array_key_exists($prop, $properties)) {
            throw new \Lib_Exception_Logic_Backtraced('Trying to set unknown property {' . $prop . '}');
        }
        $this->_checkProperty($prop, $properties[$prop], $value);
        $this->_properties[$prop] = $value;
    }

    /**
     * @param string $prop
     *
     * @return mixed
     *
     * @throws \Lib_Exception_Logic_Backtraced
     */
    final public function __get($prop)
    {
        $properties = static::GetPropertiesTypes();

        if (!array_key_exists($prop, $properties)) {
            throw new \Lib_Exception_Logic_Backtraced('Trying to get unknown property {' . $prop . '}');
        }

        return $this->_properties[$prop];
    }

    /**
     * @param string $prop
     *
     * @return bool
     *
     * @throws \Lib_Exception_Logic_Backtraced
     */
    final public function __isset($prop)
    {
        $properties = static::GetPropertiesTypes();

        if (!array_key_exists($prop, $properties)) {
            throw new \Lib_Exception_Logic_Backtraced('Trying to check unknown property {' . $prop . '}');
        }

        return null !== $this->_properties[$prop];
    }

    /**
     * @param string $prop
     *
     * @throws \Lib_Exception_Logic_Backtraced
     */
    final public function checkProperty($prop)
    {
        $properties = static::GetPropertiesTypes();

        if (!array_key_exists($prop, $properties)) {
            throw new \Lib_Exception_Logic_Backtraced('Trying to check unknown property {' . $prop . '}');
        }
        $this->_checkProperty($prop, $properties[$prop], $this->_properties[$prop]);
    }

    /**
     * @param string $prop
     * @param int $type
     * @param mixed $field_value
     *
     * @throws \Lib_Exception_Logic_Backtraced
     */
    private function _checkProperty($prop, $type, $field_value)
    {
        if (!($type & self::FLAG_NOT_NULL) && null === $field_value) {
            return;
        }

        if (is_resource($field_value)) {
            throw new \Lib_Exception_Logic_Backtraced('Property {' . $prop . '} is resource. It must not to be. Expected type: ' . self::TypeName($type));
        }

        switch ($type & self::FLAGS_MASK) {
            case self::TYPE_DATE:
            case self::TYPE_TIMESTAMP:
            case self::TYPE_DATETIME:
            case self::TYPE_INT:
                if ((int) $field_value !== $field_value) {
                    throw new \Lib_Exception_Logic_Backtraced('Property {' . $prop . '} must have type (' . self::TypeName($type) . '). Have got (' . (is_object($field_value) ? get_class($field_value) : gettype($field_value)) . ')' . (!is_null($field_value) && !is_object($field_value) ? ' with value = ' . $field_value : ''));
                }

                if ($type & self::FLAG_UNSIGNED && (int) $field_value < 0) {
                    throw new \Lib_Exception_Logic_Backtraced('Property {' . $prop . '} must be unsigned (' . self::TypeName($type) . '). Have got (' . (is_object($field_value) ? get_class($field_value) : gettype($field_value)) . ')' . (!is_null($field_value) && !is_object($field_value) ? ' with value = ' . $field_value : ''));
                }
                break;
            case self::TYPE_FLOAT:
                if ((float) $field_value !== $field_value) {
                    throw new \Lib_Exception_Logic_Backtraced('Property {' . $prop . '} must have type (' . self::TypeName($type) . '). Have got (' . (is_object($field_value) ? get_class($field_value) : gettype($field_value)) . ')' . (!is_null($field_value) && !is_object($field_value) ? ' with value = ' . $field_value : ''));
                }

                if ($type & self::FLAG_UNSIGNED && (float) $field_value < 0) {
                    throw new \Lib_Exception_Logic_Backtraced('Property {' . $prop . '} must be unsigned (' . self::TypeName($type) . '). Have got (' . (is_object($field_value) ? get_class($field_value) : gettype($field_value)) . ')' . (!is_null($field_value) && !is_object($field_value) ? ' with value = ' . $field_value : ''));
                }
                break;
            case self::TYPE_STRING:
                if ((string) $field_value !== $field_value) {
                    throw new \Lib_Exception_Logic_Backtraced('Property {' . $prop . '} must have type (' . self::TypeName($type) . '). Have got (' . (is_object($field_value) ? get_class($field_value) : gettype($field_value)) . ')' . (!is_null($field_value) && !is_object($field_value) ? ' with value = ' . $field_value : ''));
                }
                break;
            case self::TYPE_BOOL:
                if ((bool) $field_value !== $field_value) {
                    throw new \Lib_Exception_Logic_Backtraced('Property {' . $prop . '} must have type (' . self::TypeName($type) . '). Have got (' . (is_object($field_value) ? get_class($field_value) : gettype($field_value)) . ')' . (!is_null($field_value) && !is_object($field_value) ? ' with value = ' . $field_value : ''));
                }
                break;
            default:
                throw new \Lib_Exception_Logic_Backtraced('Unsupported type: (' . $type . '). For property {' . $prop . '}');
        }
    }

    /**
     * @param int $type
     *
     * @return string
     */
    final public static function TypeName($type)
    {
        switch ($type & self::FLAGS_MASK) {
            case self::TYPE_TIMESTAMP:
                return 'TYPE_TIMESTAMP';
            case self::TYPE_DATETIME:
                return 'TYPE_DATETIME';
            case self::TYPE_DATE:
                return 'TYPE_DATE';
            case self::TYPE_INT:
                return 'TYPE_INT';
            case self::TYPE_FLOAT:
                return 'TYPE_FLOAT';
            case self::TYPE_STRING:
                return 'TYPE_STRING';
            case self::TYPE_BOOL:
                return 'TYPE_BOOL';
            default:
                return $type;
        }
    }
}
