<?php

class Lib_ORM_IteratorUnbuffered extends \Lib_Iterator_Unbuffered
{
    /** @var \Lib_ORM_Object */
    private $_obj;

    /** @var bool */
    private $_for_save;

    /** @var array */
    private $_fields;

    /** @var bool */
    private $_dataset_checked = false;

    /**
     * @param Lib_ORM_Query $query
     * @param bool $for_save
     */
    public function __construct(Lib_ORM_Query $query, $for_save = false)
    {
        $this->_obj = $query->getObj();
        $this->_fields = $query->getFields();
        $this->_for_save = $for_save;
        parent::__construct($query->getDatabase(), $query->getSql(), \PDO::FETCH_ASSOC, $query->isUseSqlCalcFoundRows());
    }

    /**
     * @throws \Lib_Exception_Logic_Backtraced
     *
     * @return Lib_ORM_Object|mixed
     */
    protected function _fetchCurrent()
    {
        $row = parent::_fetchCurrent();

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
}
