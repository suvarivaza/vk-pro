<?php

/**
 * Класс итератора для выборки данных из UNBUFFERED query
 * При каждом rewind делает новое подключение, запрос и fetch первой строки
 * При каждом next делает очередной fetch и увеличение индекса текущей строки на 1
 * Итерации и выборка заканчиваются, когда fetcg в методе next возвращает false
 * Во время прерывания итераций нужно вызывать метод destory, для освобождения памяти и отключения от базы
 */
class Lib_Iterator_Unbuffered implements \Iterator
{
    /** @var string */
    private $_sql;

    /** @var string */
    private $_dbName;

    /** @var int стиль выборки, по-умолчанию \PDO::FETCH_ASSOC */
    private $_fetchStyle;

    /** @var bool по окончании выборки сделать SELECT FOUND_ROWS() */
    private $_selectTotal;

    /** @var \Lib_DB_Adapter */
    private $_db = null;

    /** @var \Lib_DB_Adapter_Statement */
    private $_res = null;

    /** @var array Результат последнего fetch-а */
    private $_current = false;

    /** @var int Индекс результата последнего fetch-а */
    private $_key = false;

    /** @var int */
    private $_total = null;

    /**
     * Создает итератор
     *
     * @param string $db_name Имя базы, к которой будет сделан UNCACHED UNBUFFERED query
     * @param string $sql Строка запроса
     * @param int $fetch_style Стиль выборки
     * @param bool $select_total Флан выборки
     */
    public function __construct($db_name, $sql, $fetch_style = \PDO::FETCH_ASSOC, $select_total = false)
    {
        $this->_dbName = $db_name;
        $this->_sql = $sql;
        $this->_fetchStyle = $fetch_style;
        $this->_selectTotal = $select_total;
    }

    /**
     * Возвращает результат последнего fetch-а
     *
     * @return array|bool|mixed
     */
    public function current()
    {
        return $this->_current;
    }

    /**
     * Делает fetch новой строки из UNBUFFERED query, если вернулся false, то расставляет признаки окончания итераций
     */
    public function next()
    {
        if (null === $this->_res) {
            return;
        }

        if (false === ($this->_current = $this->_fetchCurrent())) {
            if ($this->_selectTotal) {
                $res = $this->_db->query('SELECT FOUND_ROWS()');
                $this->_total = $res->fetchColumn();
            }
            $this->destroy();
        } else {
            $this->_total = null;
            $this->_key++;
        }
    }

    /**
     * Возвращает индекс текущей итерации, или false если итерации закончилась
     *
     * @return bool|int|string
     */
    public function key()
    {
        return $this->_key;
    }

    /**
     * Если последний fetch вернул не false, то итерация разрешена
     *
     * @return bool
     */
    public function valid()
    {
        return false !== $this->_current;
    }

    /**
     * Делает новый UNBUFFERED запрос данных и дергает первую строку
     */
    public function rewind()
    {
        $info = \Lib_DB_Factory::GetInfo($this->_dbName);
        $this->_db = \Lib_DB_Factory::GetUncachedInstance($info);
        //$this->_db->query( 'SET SQL_BIG_SELECTS=1' );
        $this->_db->setOption(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, 0);
        $this->_res = $this->_db->query($this->_sql);

        if ($this->_res) {
            $this->_current = $this->_fetchCurrent();
            $this->_key = 0;
            $this->_total = null;
        }
    }

    /**
     * Делает выборку строки из базы
     * Этот метод нужно переопределять в потомках, для получения объектов моделей
     *
     * @return mixed
     */
    protected function _fetchCurrent()
    {
        $res = $this->_res->fetch($this->_fetchStyle);

        if ($res === null || $res === false) {
            return false;
        }

        return $res;
    }

    /**
     * @return int|null
     */
    public function getTotal()
    {
        return $this->_total;
    }

    public function destroy()
    {
        $this->_key = false;

        if ($this->_res) {
            $this->_res->close();
            $this->_res = null;
        }

        if ($this->_db) {
            $this->_db->close();
            $this->_db = null;
        }
    }
}
