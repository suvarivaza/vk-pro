<?php

/**
 * Class Lib_ORM_SphinxQuery.
 */
class Lib_ORM_SphinxQuery extends Lib_ORM_Query
{
    /**
     * @see sqlCalcFoundRows()
     *
     * @var bool
     */
    private $_count;

    /**
     * @param bool $flag
     *
     * @return $this|Lib_ORM_Query
     */
    public function sqlCalcFoundRows($flag)
    {
        $this->_count = (bool) $flag;

        return $this;
    }

    /**
     * @see sqlCalcFoundRows()
     *
     * @return bool
     */
    public function isUseSqlCalcFoundRows()
    {
        return $this->_count;
    }

    /**
     * This implementation ignores distinct flag.
     *
     * @param bool $flag
     *
     * @return Lib_ORM_SphinxQuery
     */
    public function distinct($flag)
    {
        return $this;
    }

    /**
     * @return Lib_ORM_SphinxIterator
     */
    public function iterator()
    {
        return new Lib_ORM_SphinxIterator($this, $this->isFromMaster());
    }

    /**
     * @return Lib_ORM_SphinxIterator
     */
    public function iteratorForSave()
    {
        return new Lib_ORM_SphinxIterator($this, $this->isFromMaster(), true);
    }

    /**
     * @throws Lib_Exception_Logic
     */
    public function iteratorUnbuffered()
    {
        throw new \Lib_Exception_Logic(
            'Unbuffered iterators are not implemented.'
        );
    }

    /**
     * @throws Lib_Exception_Logic
     */
    public function iteratorUnbufferedForSave()
    {
        throw new \Lib_Exception_Logic(
            'Unbuffered iterators are not implemented.'
        );
    }
}
