<?php

class Lib_ORM_Filter_Condition_Range extends Lib_ORM_Filter_Condition
{
    private static $_operations = [
        'BETWEEN', 'NOT BETWEEN',
    ];

    /** @var mixed */
    private $_first;

    /** @var mixed */
    private $_second;

    /**
     * @param string $field
     * @param int $type
     * @param string $operation
     * @param mixed $first
     * @param mixed $second
     *
     * @throws \Lib_Exception_InvalidArgument_Type_Backtraced
     * @throws \Lib_Exception_InvalidArgument_Backtraced
     */
    public function __construct($field, $type, $operation, $first, $second)
    {
        parent::__construct($field, $type);

        if (!in_array($operation, self::$_operations)) {
            throw new \Lib_Exception_InvalidArgument_Backtraced('Unsupported pair operation: ' . $operation);
        }

        if (!is_scalar($first)) {
            throw new \Lib_Exception_InvalidArgument_Type_Backtraced($first, 'scalar');
        }

        if (!is_scalar($second)) {
            throw new \Lib_Exception_InvalidArgument_Type_Backtraced($second, 'scalar');
        }
        $this->_checkValue($type, $field, $first);
        $this->_checkValue($type, $field, $second);
        $this->_operation = $operation;
        $this->_first = $first;
        $this->_second = $second;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $sql = '`' . $this->_field . '` ' . $this->_operation . ' ';

        switch ($this->_type) {
            case Lib_ORM_Object::TYPE_DATE:
                $sql .= 'STR_TO_DATE( \'' . \Lib_TimeStamp::createFromTimestamp($this->_first)->formatMySqlDate() . '\', \'%Y-%m-%d\' ) AND STR_TO_DATE( \'' . \Lib_TimeStamp::createFromTimestamp($this->_second)->formatMySqlDate() . '\', \'%Y-%m-%d\' )';
                break;
            case Lib_ORM_Object::TYPE_TIMESTAMP:
            case Lib_ORM_Object::TYPE_DATETIME:
                $sql .= 'FROM_UNIXTIME( ' . $this->_first . ' ) AND FROM_UNIXTIME( ' . $this->_second . ' )';
                break;
            case Lib_ORM_Object::TYPE_STRING:
                $sql .= '\'' . addslashes($this->_first) . '\' AND \'' . addslashes($this->_second) . '\'';
                break;
            case Lib_ORM_Object::TYPE_INT:
                $sql .= $this->_first . ' AND ' . $this->_second;
                break;
            case Lib_ORM_Object::TYPE_FLOAT:
                $sql .= \Lib_DB::NormalizeFloat($this->_first) . ' AND ' . \Lib_DB::NormalizeFloat($this->_second);
                break;
            default:
                return '( 1 = 0 /* error: Unsupported field type: ' . Lib_ORM_Object::TypeName($this->_type) . ' for field ' . $this->_field . ' */ )';
        }

        return $sql;
    }
}
