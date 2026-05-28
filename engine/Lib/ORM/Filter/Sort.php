<?php

class Lib_ORM_Filter_Sort
{
    /** @var array */
    private static $_directions = [
        'ASC',
        'DESC',
    ];

    /** @var array */
    private static $_functions = [
        'RAND()',
    ];

    /** @var array */
    private $_sort = [];

    /** @var array */
    private $_added_fields = [];

    /**
     * @param string $field
     * @param string $direction
     *
     * @throws \Lib_Exception_Logic_Backtraced
     * @throws \Lib_Exception_InvalidArgument_Backtraced
     */
    public function addField($field, $direction)
    {
        if (!in_array($direction, self::$_directions)) {
            throw new \Lib_Exception_InvalidArgument_Backtraced('Unsupported direction: ' . $direction);
        }

        if (in_array($field, $this->_added_fields)) {
            throw new \Lib_Exception_Logic_Backtraced('Field ' . $field . ' already added');
        }
        $this->_added_fields[] = $field;
        $this->_sort[] = [
            'field' => $field,
            'direction' => $direction,
        ];
    }

    /**
     * @param string $function
     *
     * @throws \Lib_Exception_InvalidArgument_Backtraced
     */
    public function addFunction($function)
    {
        if (!in_array($function, self::$_functions)) {
            throw new \Lib_Exception_InvalidArgument_Backtraced('Unsupported function: ' . $function);
        }
        $this->_sort[] = ['function' => $function];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $sort = [];

        foreach ($this->_sort as $item) {
            if (isset($item['field'])) {
                $sort[] = '`' . $item['field'] . '` ' . $item['direction'];
            } elseif (isset($item['function'])) {
                $sort[] = $item['function'];
            }
        }

        return join(', ', $sort);
    }
}
