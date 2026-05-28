<?php

/**
 * Итератор, создаваемый запросом данных
 */
abstract class Lib_Iterator_QueryDataIterator implements Countable, Iterator
{
    protected $data = [];
    private $objects = [];
    private $loaded = false;
    private $_currentData = [];

    /**
     * Конструктор
     */
    public function __construct()
    {
    }

    /**
     * Загрузка данных
     */
    public function load()
    {
        if (true === $this->loaded) {
            return;
        }

        $this->getdata();
        $this->loaded = true;
    }

    /**
     * Формирование данных
     */
    abstract public function getdata();

    /**
     * Создание объекта
     *
     * @param array $data
     *
     * @return array
     */
    public function getobject($data)
    {
        return $data;
    }

    /**
     * @return array
     */
    public function getCurrentData()
    {
        return $this->_currentData;
    }

    // Iterator
    public function current()
    {
        if ($this->data === null) {
            return null;
        }

        $this->load();

        $k = key($this->data);

        if (!isset($this->objects[$k]) && isset($this->data[$k])) {
            $this->_currentData = $this->data[$k];
            $this->objects[$k] = $this->getobject($this->_currentData);
        }

        if (!isset($this->objects[$k])) {
            return null;
        }

        return $this->objects[$k];
    }

    public function key()
    {
        $this->load();

        if ($this->data !== null) {
            return key($this->data);
        }

        return null;
    }

    public function next()
    {
        $this->load();

        if ($this->data !== null) {
            return next($this->data) !== false;
        }

        return null;
    }

    public function rewind()
    {
        $this->load();

        if ($this->data !== null) {
            return reset($this->data);
        }

        return null;
    }

    public function valid()
    {
        $this->load();

        if ($this->data !== null) {
            return current($this->data) !== false;
        }

        return null;
    }

    // Countable
    public function count()
    {
        $this->load();

        if ($this->data === null) {
            return 0;
        }

        return sizeof($this->data);
    }
}
