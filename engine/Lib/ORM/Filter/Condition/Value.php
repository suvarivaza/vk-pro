<?php

class Lib_ORM_Filter_Condition_Value extends Lib_ORM_Filter_Condition
{
    private static $_operations = [
        '=', '!=', '>', '<', '>=', '<=', '&', '|', '^', 'LIKE', 'NOT LIKE',
    ];

    /** @var mixed */
    private $_value;

    /**
     * @param string $field
     * @param int $type
     * @param string $operation
     * @param mixed $value
     *
     * @throws \Lib_Exception_InvalidArgument_Type_Backtraced
     * @throws \Lib_Exception_InvalidArgument_Backtraced
     */
    public function __construct($field, $type, $operation, $value)
    {
        parent::__construct($field, $type);

        if (!in_array($operation, self::$_operations)) {
            throw new \Lib_Exception_InvalidArgument_Backtraced('Unsupported value operation: ' . $operation);
        }

        if (!is_scalar($value)) {
            throw new \Lib_Exception_InvalidArgument_Type_Backtraced($value, 'scalar');
        }

        if ($operation != 'LIKE' && $operation != 'NOT LIKE') {
            $this->_checkValue($type, $field, $value);
        }
        $this->_operation = $operation;
        $this->_value = $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        switch ($this->_operation) {
            case 'IS NULL':
            case 'NOT IS NULL':
                return '`' . $this->_field . '` ' . $this->_operation;
            case 'LIKE':
            case 'NOT LIKE':
                return '`' . $this->_field . '` ' . $this->_operation . ' \'' . addslashes($this->_value) . '\'';
            default:
                $sql = '`' . $this->_field . '` ' . $this->_operation . ' ';

                switch ($this->_type) {
                    case Lib_ORM_Object::TYPE_DATE:
                        $sql .= 'STR_TO_DATE( \'' . \Lib_TimeStamp::createFromTimestamp($this->_value)->formatMySqlDate() . '\', \'%Y-%m-%d\' )';
                        break;
                    case Lib_ORM_Object::TYPE_TIMESTAMP:
                    case Lib_ORM_Object::TYPE_DATETIME:
                        $sql .= 'FROM_UNIXTIME( ' . $this->_value . ' )';
                        break;
                    case Lib_ORM_Object::TYPE_STRING:
                        $sql .= '\'' . addslashes($this->_value) . '\'';
                        break;
                    case Lib_ORM_Object::TYPE_BOOL:
                        $sql .= $this->_value ? '1' : '0';
                        break;
                    case Lib_ORM_Object::TYPE_INT:
                        $sql .= $this->_value;
                        break;
                    case Lib_ORM_Object::TYPE_FLOAT:
                        $sql .= \Lib_DB::NormalizeFloat($this->_value);
                        break;
                    default:
                        return '( 1 = 0 /* error: Unsupported field type: ' . Lib_ORM_Object::TypeName($this->_type) . ' for field ' . $this->_field . ' */ )';
                }
        }

        return $sql;
    }
}
