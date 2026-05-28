<?php

class Lib_ORM_Filter_Condition_Collection extends Lib_ORM_Filter_Condition
{
    private static $_operations = [
        'IN', 'NOT IN',
    ];

    /** @var array */
    private $_collection;

    /**
     * @param string $field
     * @param int $type
     * @param string $operation
     * @param array $collection
     *
     * @throws Lib_Exception_InvalidArgument_Backtraced
     * @throws Lib_Exception_InvalidArgument_Type_Backtraced
     */
    public function __construct($field, $type, $operation, array $collection)
    {
        parent::__construct($field, $type);

        if (!in_array($operation, self::$_operations)) {
            throw new \Lib_Exception_InvalidArgument_Backtraced('Unsupported collection operation: ' . $operation);
        }

        if (!is_array($collection)) {
            throw new \Lib_Exception_InvalidArgument_Type_Backtraced($collection, 'array');
        }

        if (!count($collection)) {
            throw new \Lib_Exception_InvalidArgument_Backtraced('Empty collections not allowed');
        }

        foreach ($collection as $val) {
            if (!is_scalar($val)) {
                throw new \Lib_Exception_InvalidArgument_Backtraced('Collection must to consists of scalars');
            }
            $this->_checkValue($type, $field, $val);
        }
        $this->_operation = $operation;
        $this->_collection = $collection;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $sql = '`' . $this->_field . '` ' . $this->_operation . ' ( ';

        switch ($this->_type) {
            case Lib_ORM_Object::TYPE_DATE:
                $sql .= 'STR_TO_DATE(\'' . join('\', \'%Y-%m-%d\'), STR_TO_DATE(\'', array_map(function ($e) {
                    return \Lib_TimeStamp::createFromTimestamp($e)->formatMySqlDate();
                }, $this->_collection)) . '\', \'%Y-%m-%d\' )';
                break;
            case Lib_ORM_Object::TYPE_TIMESTAMP:
            case Lib_ORM_Object::TYPE_DATETIME:
                $sql .= 'FROM_UNIXTIME(' . join('), FROM_UNIXTIME(', $this->_collection) . ')';
                break;
            case Lib_ORM_Object::TYPE_STRING:
                $sql .= '\'' . join('\', \'', array_map('addslashes', $this->_collection)) . '\'';
                break;
            case Lib_ORM_Object::TYPE_INT:
                $sql .= join(', ', $this->_collection);
                break;
            case Lib_ORM_Object::TYPE_FLOAT:
                $sql .= join(', ', array_map('\Lib_DB::NormalizeFloat', $this->_collection));
                break;
            default:
                return '( 1 = 0 /* error: Unsupported field type: ' . Lib_ORM_Object::TypeName($this->_type) . ' for field ' . $this->_field . ' */ )';
        }
        $sql .= ' )';

        return $sql;
    }
}
