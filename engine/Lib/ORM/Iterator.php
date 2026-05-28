<?php

class Lib_ORM_Iterator implements \Iterator, \Countable
{
    /** @var bool */
    private $_dataset_checked = false;

    /** @var array */
    private $_data;

    /** @var bool */
    private $_for_save;

    /** @var Lib_ORM_Object */
    private $_obj;

    /** @var array */
    private $_fields;

    /** @var int */
    private $_total = null;

    /**
     * @param Lib_ORM_Query $query
     * @param bool $for_save
     */
    public function __construct(Lib_ORM_Query $query, $for_save = false)
    {
        $this->_obj = $query->getObj();
        $this->_fields = $query->getFields();
        $this->_data = [];
        $this->_for_save = $for_save;

        $db = \Lib_DB_Factory::GetInstance($query->getDatabase());

        $res = $db->query($query->getSql());

        while ($row = $this->fetchCurrent($res)) {
            $this->_data[] = $row;
        }

        if ($query->isUseSqlCalcFoundRows()) {
            $this->_total = $this->_getTotal($db);
        }

        $db->close();
    }

    /**
     * @param Lib_DB_Adapter $db
     *
     * @return int
     */
    protected function _getTotal(\Lib_DB_Adapter $db)
    {
        return (int) $db->query('SELECT FOUND_ROWS()')->fetchColumn();
    }

    /**
     * @return int|null
     */
    public function getTotal()
    {
        return $this->_total;
    }

    /**
     * @param \Lib_DB_Adapter_Statement $res
     *
     * @return bool|Lib_ORM_Object
     *
     * @throws \Lib_Exception_Logic_Backtraced
     */
    protected function fetchCurrent(\Lib_DB_Adapter_Statement $res)
    {
        $row = $res->fetch_assoc();

        if (!is_array($row)) {
            return false;
        }
        $obj = clone $this->_obj;

        foreach ($this->_fields as $name => $type) {
            if (!$this->_dataset_checked && !array_key_exists($name, $row)) {
                throw new \Lib_Exception_Logic_Backtraced('Field ' . $name . ' is not present in row. For ' . get_class($obj));
            }

            if (($type == Lib_ORM_Object::TYPE_DATETIME || $type == Lib_ORM_Object::TYPE_TIMESTAMP) && null !== $row[$name]) {
                call_user_func([$obj, '__set'], $name, \Lib_TimeStamp::createFromFormat(\Lib_TimeStamp::MYSQL_FORMAT, $row[$name])->getTimestamp());
            } elseif ($type == Lib_ORM_Object::TYPE_DATE && null !== $row[$name]) {
                call_user_func([$obj, '__set'], $name, \Lib_TimeStamp::createFromFormat('Y-m-d', $row[$name])->getTimestamp());
            } elseif ($type == Lib_ORM_Object::TYPE_INT && null !== $row[$name]) {
                call_user_func([$obj, '__set'], $name, (int) $row[$name]);
            } elseif ($type == Lib_ORM_Object::TYPE_BOOL && null !== $row[$name]) {
                call_user_func([$obj, '__set'], $name, (bool) $row[$name]);
            } elseif ($type == Lib_ORM_Object::TYPE_FLOAT && null !== $row[$name]) {
                call_user_func([$obj, '__set'], $name, (float) $row[$name]);
            } else {
                call_user_func([$obj, '__set'], $name, $row[$name]);
            }
        }
        $this->_dataset_checked = true;

        if ($this->_for_save) {
            $obj->makeShadow();
        }

        return $obj;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     *
     * @link http://php.net/manual/en/iterator.current.php
     *
     * @return mixed Can return any type.
     */
    public function current()
    {
        return current($this->_data);
    }


    public function count()
    {
        return count($this->_data);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     *
     * @link http://php.net/manual/en/iterator.next.php
     */
    public function next()
    {
        next($this->_data);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     *
     * @link http://php.net/manual/en/iterator.key.php
     *
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return key($this->_data);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     *
     * @link http://php.net/manual/en/iterator.valid.php
     *
     * @return bool The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return false !== current($this->_data);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     */
    public function rewind()
    {
        reset($this->_data);
    }
}
