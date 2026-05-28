<?php

class Lib_ORM_Filter
{
    /** @var array */
    private $_fields;

    /** @var Lib_ORM_Filter_Condition */
    private $_condition;

    /** @var bool */
    private $_default_aggregator_closed = false;

    /** @var bool */
    private $_use_aggregator = false;

    /** @var Lib_ORM_Filter_Condition_Aggregator[] */
    private $_aggregator_stack;

    /** @var Lib_ORM_Filter_Condition_Aggregator */
    private $_current_aggregator;

    /**
     * @param Lib_ORM_Object $obj
     * @param string $aggregator_operation
     */
    public function __construct(Lib_ORM_Object $obj, $aggregator_operation = 'AND')
    {
        $this->_fields = $obj->GetPropertiesTypesNoFlags();
        $this->aggregatorOpen($aggregator_operation);
    }

    /**
     * @param string $field
     * @param string $operation
     *
     * @return \Lib_ORM_Filter
     *
     * @throws \Lib_Exception_Logic_Backtraced
     * @throws \Lib_Exception_InvalidArgument_Backtraced
     */
    public function fieldCheck($field, $operation)
    {
        if (!$this->_use_aggregator) {
            throw new \Lib_Exception_Logic_Backtraced('Must to call aggregatorOpen first');
        }

        if (!array_key_exists($field, $this->_fields)) {
            throw new \Lib_Exception_InvalidArgument_Backtraced('Unknown field: ' . $field);
        }
        $condition = new Lib_ORM_Filter_Condition_Check($field, $this->_fields[$field], $operation);
        $this->_current_aggregator->addCondition($condition);

        return $this;
    }

    /**
     * @param string $field
     * @param string $operation
     * @param mixed $value
     *
     * @return \Lib_ORM_Filter
     *
     * @throws \Lib_Exception_Logic_Backtraced
     * @throws \Lib_Exception_InvalidArgument_Backtraced
     */
    public function fieldValue($field, $operation, $value)
    {
        if (!$this->_use_aggregator) {
            throw new \Lib_Exception_Logic_Backtraced('Must to call aggregatorOpen first');
        }

        if (!array_key_exists($field, $this->_fields)) {
            throw new \Lib_Exception_InvalidArgument_Backtraced('Unknown field: ' . $field);
        }
        $condition = new Lib_ORM_Filter_Condition_Value($field, $this->_fields[$field], $operation, $value);
        $this->_current_aggregator->addCondition($condition);

        return $this;
    }

    /**
     * @param string $field
     * @param string $operation
     * @param mixed $first
     * @param mixed $second
     *
     * @return \Lib_ORM_Filter
     *
     * @throws \Lib_Exception_Logic_Backtraced
     * @throws \Lib_Exception_InvalidArgument_Backtraced
     */
    public function fieldRange($field, $operation, $first, $second)
    {
        if (!$this->_use_aggregator) {
            throw new \Lib_Exception_Logic_Backtraced('Must to call aggregatorOpen first');
        }

        if (!array_key_exists($field, $this->_fields)) {
            throw new \Lib_Exception_InvalidArgument_Backtraced('Unknown field: ' . $field);
        }
        $condition = new Lib_ORM_Filter_Condition_Range($field, $this->_fields[$field], $operation, $first, $second);
        $this->_current_aggregator->addCondition($condition);

        return $this;
    }

    /**
     * @param string $field
     * @param string $operation
     * @param array $collection
     *
     * @return \Lib_ORM_Filter
     *
     * @throws \Lib_Exception_Logic_Backtraced
     * @throws \Lib_Exception_InvalidArgument_Backtraced
     */
    public function fieldCollection($field, $operation, array $collection)
    {
        if (!$this->_use_aggregator) {
            throw new \Lib_Exception_Logic_Backtraced('Must to call aggregatorOpen first');
        }

        if (!array_key_exists($field, $this->_fields)) {
            throw new \Lib_Exception_InvalidArgument_Backtraced('Unknown field: ' . $field);
        }
        $condition = new Lib_ORM_Filter_Condition_Collection($field, $this->_fields[$field], $operation, $collection);
        $this->_current_aggregator->addCondition($condition);

        return $this;
    }

    /**
     * @param $operation
     *
     * @return \Lib_ORM_Filter
     */
    public function aggregatorOpen($operation)
    {
        $this->_use_aggregator = true;
        $aggregator = new Lib_ORM_Filter_Condition_Aggregator([], $operation);
        $this->_aggregator_stack[] = $aggregator;

        if ($this->_current_aggregator) {
            $this->_current_aggregator->addCondition($aggregator);
        }
        $this->_current_aggregator = $aggregator;

        return $this;
    }

    /**
     * @throws \Lib_Exception_Logic_Backtraced
     */
    public function aggregatorClose()
    {
        if (!$this->_use_aggregator) {
            throw new \Lib_Exception_Logic_Backtraced('Must to call aggregatorOpen first');
        }
        array_pop($this->_aggregator_stack);
        $count = count($this->_aggregator_stack);

        if ($count) {
            $this->_current_aggregator = $this->_aggregator_stack[$count - 1];
        } else {
            $this->_condition = $this->_current_aggregator;
        }

        return $this;
    }

    /**
     * @return string
     *
     * @throws \Lib_Exception_Logic_Backtraced
     */
    public function getSql()
    {
        if (!$this->_default_aggregator_closed) {
            $this->aggregatorClose();
            $this->_default_aggregator_closed = true;
        }

        if (count($this->_aggregator_stack)) {
            throw new \Lib_Exception_Logic_Backtraced('Before getSql must to call aggregatorClose many times as nessesary to close all aggregators');
        }

        if (null === $this->_condition) {
            throw new \Lib_Exception_Logic_Backtraced('Condition not constructed yet');
        }

        return $this->_condition->__toString();
    }
}
