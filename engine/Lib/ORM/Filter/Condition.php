<?php

abstract class Lib_ORM_Filter_Condition
{
    /** @var array */
    protected static $_types = [
        Lib_ORM_Object::TYPE_BOOL,
        Lib_ORM_Object::TYPE_INT,
        Lib_ORM_Object::TYPE_FLOAT,
        Lib_ORM_Object::TYPE_STRING,
        Lib_ORM_Object::TYPE_DATETIME,
        Lib_ORM_Object::TYPE_TIMESTAMP,
        Lib_ORM_Object::TYPE_DATE,
    ];

    /** @var string */
    protected $_field;

    /** @var int */
    protected $_type;

    /** @var int */
    protected $_operation;

    /**
     * @param string $field
     * @param int $type
     *
     * @throws \Lib_Exception_InvalidArgument_Backtraced
     */
    public function __construct($field, $type)
    {
        if (!in_array($type, self::$_types)) {
            throw new \Lib_Exception_InvalidArgument_Backtraced('Unsupported field type: ' . Lib_ORM_Object::TypeName($type) . '. For field: ' . $field);
        }
        $this->_field = $field;
        $this->_type = $type;
    }

    /**
     * @return string
     */
    abstract public function __toString();

    /**
     * @param int $type
     * @param string $prop
     * @param mixed $field_value
     *
     * @throws Lib_Exception_Logic_Backtraced
     */
    protected function _checkValue($type, $prop, $field_value)
    {
        switch ($type) {
            case Lib_ORM_Object::TYPE_DATE:
            case Lib_ORM_Object::TYPE_TIMESTAMP:
            case Lib_ORM_Object::TYPE_DATETIME:
            case Lib_ORM_Object::TYPE_INT:
                if ((int) $field_value !== $field_value) {
                    throw new \Lib_Exception_Logic_Backtraced('Value of property {' . $prop . '} must have type (' . Lib_ORM_Object::TypeName($type) . '). Have got (' . (is_object($field_value) ? get_class($field_value) : gettype($field_value)) . ')' . (!is_null($field_value) && !is_object($field_value) ? ' with value = ' . $field_value : ''));
                }
                break;
            case Lib_ORM_Object::TYPE_FLOAT:
                if ((float) $field_value !== $field_value) {
                    throw new \Lib_Exception_Logic_Backtraced('Value of property {' . $prop . '} must have type (' . Lib_ORM_Object::TypeName($type) . '). Have got (' . (is_object($field_value) ? get_class($field_value) : gettype($field_value)) . ')' . (!is_null($field_value) && !is_object($field_value) ? ' with value = ' . $field_value : ''));
                }
                break;
            case Lib_ORM_Object::TYPE_STRING:
                if ((string) $field_value !== $field_value) {
                    throw new \Lib_Exception_Logic_Backtraced('Value of property {' . $prop . '} must have type (' . Lib_ORM_Object::TypeName($type) . '). Have got (' . (is_object($field_value) ? get_class($field_value) : gettype($field_value)) . ')' . (!is_null($field_value) && !is_object($field_value) ? ' with value = ' . $field_value : ''));
                }
                break;
            case Lib_ORM_Object::TYPE_BOOL:
                if ((bool) $field_value !== $field_value) {
                    throw new \Lib_Exception_Logic_Backtraced('Value of property {' . $prop . '} must have type (' . Lib_ORM_Object::TypeName($type) . '). Have got (' . (is_object($field_value) ? get_class($field_value) : gettype($field_value)) . ')' . (!is_null($field_value) && !is_object($field_value) ? ' with value = ' . $field_value : ''));
                }
                break;
            default:
                throw new \Lib_Exception_Logic_Backtraced('Unsupported type: (' . $type . '). For value of property {' . $prop . '}');
        }
    }
}
