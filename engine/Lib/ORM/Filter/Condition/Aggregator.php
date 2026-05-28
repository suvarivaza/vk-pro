<?php

class Lib_ORM_Filter_Condition_Aggregator extends Lib_ORM_Filter_Condition
{
    private static $_operations = [
        'AND',
        'OR',
    ];

    /** @var Lib_ORM_Filter_Condition[] */
    private $_conditions;

    /**
     * @param array $conditions
     * @param string $operation
     *
     * @throws \Lib_Exception_InvalidArgument_Type_Backtraced
     * @throws \Lib_Exception_InvalidArgument_Backtraced
     */
    public function __construct(array $conditions, $operation)
    {
        if (!in_array($operation, self::$_operations)) {
            throw new \Lib_Exception_InvalidArgument_Backtraced('Unsupported aggregator operation: ' . $operation);
        }

        foreach ($conditions as $condition) {
            if (!$condition instanceof Lib_ORM_Filter_Condition) {
                throw new \Lib_Exception_InvalidArgument_Type_Backtraced($condition, 'Lib_ORM_Filter_Condition');
            }
        }
        $this->_operation = $operation;
        $this->_conditions = $conditions;
    }

    /**
     * @param Lib_ORM_Filter_Condition $condition
     */
    public function addCondition(Lib_ORM_Filter_Condition $condition)
    {
        $this->_conditions[] = $condition;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if (!count($this->_conditions)) {
            return '';
        }

        return '(' . join(' ' . $this->_operation . ' ', $this->_conditions) . ')';
    }
}
