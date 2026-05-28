<?php

/**
 * @property Lib_ORM_Filter $filter
 */
class Lib_ORM_Query
{
    /** @var Lib_ORM_Object */
    private $_obj;

    /** @var array */
    private $_fields;

    /** @var string */
    private $_dbname;

    /** @var string */
    private $_tablename;

    /** @var bool */
    private $_distinct;

    /** @var bool */
    private $_sqlCalcFoundRows;

    /** @var bool */
    private $_no_cache;

    /** @var int */
    private $_limit;

    /** @var Lib_ORM_Filter */
    private $_filter;

    /** @var int */
    private $_offset;

    /** @var Lib_ORM_Filter_Sort */
    private $_sort;

    /** @var string */
    private $_filter_aggragator_operation;

    /**
     * @param Lib_ORM_Object $obj
     * @param string $db
     * @param string $table
     * @param string $aggregator_operation
     */
    public function __construct(Lib_ORM_Object $obj, $db, $table, $aggregator_operation = 'AND')
    {
        $this->_obj = $obj;
        $this->_fields = $obj->GetPropertiesTypesNoFlags();
        $this->_dbname = $db;
        $this->_tablename = $table;
        $this->_filter_aggragator_operation = $aggregator_operation;
    }

    /**
     * @param string $name
     *
     * @return Lib_ORM_Filter
     *
     * @throws \Lib_Exception_UnknownProperty_Backtraced
     */
    public function __get($name)
    {
        switch ($name) {
            case 'filter':
                if (null === $this->_filter) {
                    $this->_filter = new Lib_ORM_Filter($this->_obj, $this->_filter_aggragator_operation);
                }

                return $this->_filter;
            default:
                throw new \Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
        }
    }

    /**
     * @return Lib_ORM_Object
     */
    public function getObj()
    {
        return $this->_obj;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * @param string $field
     * @param string $direction
     *
     * @return \Lib_ORM_Query
     *
     * @throws \Lib_Exception_InvalidArgument_Backtraced
     */
    public function sort($field, $direction)
    {
        if (null === $this->_sort) {
            $this->_createSort();
        }

        if (!array_key_exists($field, $this->_fields)) {
            throw new \Lib_Exception_InvalidArgument_Backtraced('Unknown field: ' . $field);
        }
        $this->_sort->addField($field, $direction);

        return $this;
    }

    /**
     * @param string $function
     *
     * @return \Lib_ORM_Query
     */
    public function sortFunction($function)
    {
        if (null === $this->_sort) {
            $this->_createSort();
        }
        $this->_sort->addFunction($function);

        return $this;
    }

    private function _createSort()
    {
        $this->_sort = new Lib_ORM_Filter_Sort();
    }

    /**
     * @param $flag bool
     *
     * @return \Lib_ORM_Query
     */
    public function distinct($flag)
    {
        $this->_distinct = $flag;

        return $this;
    }

    public function getDatabase()
    {
        return $this->_dbname;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->_tablename;
    }

    /**
     * @param $flag bool
     *
     * @return \Lib_ORM_Query
     */
    public function sqlCalcFoundRows($flag)
    {
        $this->_sqlCalcFoundRows = $flag;

        return $this;
    }

    /**
     * @return bool
     */
    public function isUseSqlCalcFoundRows()
    {
        return $this->_sqlCalcFoundRows;
    }

    /**
     * @param $n int
     *
     * @return \Lib_ORM_Query
     */
    public function limit($n)
    {
        $this->_limit = $n;

        return $this;
    }

    /**
     * @param $n int
     *
     * @return \Lib_ORM_Query
     */
    public function offset($n)
    {
        $this->_offset = $n;

        return $this;
    }

    public function noCahce($n)
    {
        $this->_no_cache = $n;

        return $this;
    }

    /**
     * @return string
     *
     * @throws \Lib_Exception_Logic_Backtraced
     */
    public function getSql()
    {
        $sql = 'SELECT';

        if ($this->_no_cache) {
            $sql .= ' SQL_NO_CACHE';
        }

        if ($this->_distinct) {
            $sql .= ' DISTINCT';
        }

        if ($this->_sqlCalcFoundRows) {
            $sql .= ' SQL_CALC_FOUND_ROWS';
        }

        $sql .= ' `' . implode('`, `', array_keys($this->_fields)) . '`';
        $sql .= ' FROM `' . $this->_tablename . '`';

        $where = null !== $this->_filter ? $this->_filter->getSql() : false;

        if ($where) {
            $sql .= ' WHERE ' . $where;
        } elseif (null === $this->_limit) {
            throw new \Lib_Exception_Logic_Backtraced('Empty filter with empty limit not allowed');
        }

        if (null !== $this->_sort) {
            $sort = $this->_sort->__toString();

            if ($sort) {
                $sql .= ' ORDER BY ' . $sort;
            }
        }

        if (null !== $this->_limit && $this->_limit > 0) {
            $sql .= ' LIMIT ';

            if (null !== $this->_offset && $this->_offset > 0) {
                $sql .= $this->_offset . ', ';
            }
            $sql .= $this->_limit;
        }

        return $sql;
    }

    /**
     * @return Lib_ORM_IteratorUnbuffered
     */
    public function iteratorUnbuffered()
    {
        return new Lib_ORM_IteratorUnbuffered($this);
    }

    /**
     * @return Lib_ORM_IteratorUnbuffered
     */
    public function iteratorUnbufferedForSave()
    {
        return new Lib_ORM_IteratorUnbuffered($this, true);
    }

    /**
     * @return Lib_ORM_Iterator
     */
    public function iterator()
    {
        return new Lib_ORM_Iterator($this);
    }

    /**
     * @return Lib_ORM_Iterator
     */
    public function iteratorForSave()
    {
        return new Lib_ORM_Iterator($this, true);
    }
}
