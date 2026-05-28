<?php

class Lib_ORM_Filter_Condition_Check extends Lib_ORM_Filter_Condition
{
    private static $_operations = [
        'IS NULL', 'IS NOT NULL',
    ];

    /**
     * @param string $field
     * @param int $type
     * @param string $operation
     *
     * @throws \Lib_Exception_InvalidArgument_Backtraced
     */
    public function __construct($field, $type, $operation)
    {
        parent::__construct($field, $type);

        if (!in_array($operation, self::$_operations)) {
            throw new \Lib_Exception_InvalidArgument_Backtraced('Unsupported value operation: ' . $operation);
        }
        $this->_operation = $operation;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '`' . $this->_field . '` ' . $this->_operation;
    }
}
