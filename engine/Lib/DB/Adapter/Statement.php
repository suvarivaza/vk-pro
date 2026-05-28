<?php

/**
 * Результата запроса
 *
 * @throws Lib_Exception_InvalidArgument
 *
 * @property int $num_rows
 * @property string $query
 */
class Lib_DB_Adapter_Statement
{
    /**
     * Объект PDOStatement
     *
     * @var PDOStatement
     */
    private $_object = null;

    /**
     * IP сервера БД
     *
     * @var string
     */
    private $_host_ip = null;

    /**
     * Имя БД
     *
     * @var string
     */
    private $_dbname = null;

    /**
     * Задействованные привязки параметров и значений
     *
     * @var array
     */
    private $_binds = [];

    /**
     * Конструктор
     *
     * @throws Lib_Exception_InvalidArgument
     *
     * @param PDOStatement $object
     * @param string $host_ip
     * @param string $dbname
     */
    public function __construct(PDOStatement $object, $host_ip, $dbname)
    {
        $this->_object = $object;
        $this->_host_ip = $host_ip;
        $this->_dbname = $dbname;
    }

    /**
     * Деструктор
     */
    public function __destruct()
    {
        if ($this->_object === null) {
            return;
        }
        $this->_object->closeCursor();
        unset($this->_object);
    }

    /**
     * Получает строку результата
     *
     * @param int $fetch_style Способ получения
     * @param int $cursor_orientation Ориентация курсора
     * @param int $cursor_offset Смещение курсора
     *
     * @return mixed
     */
    public function fetch($fetch_style = PDO::FETCH_BOTH, $cursor_orientation = PDO::FETCH_ORI_NEXT, $cursor_offset = 0)
    {
        $result = $this->_object->fetch($fetch_style, $cursor_orientation, $cursor_offset);

        if ($result === false) {
            return null;
        }

        return $result;
    }

    /**
     * Получает весь результат в виде массива
     *
     * @param int $fetch_style Способ получения
     * @param $fetch_argument
     * @param array $ctor_args
     *
     * @return array|bool
     */
    public function fetchAll($fetch_style = PDO::FETCH_BOTH, $fetch_argument = null, $ctor_args = [])
    {
        switch (func_num_args()) {
            case 0:
            case 1:
                return $this->_object->fetchAll($fetch_style);
            case 2:
                return $this->_object->fetchAll($fetch_style, $fetch_argument);
            case 3:
                return $this->_object->fetchAll($fetch_style, $fetch_argument, $ctor_args);
        }

        return false;
    }

    /**
     * Получает весь результат в виде массива
     * (для совместимости с mysqli_result)
     *
     * @param int $fetch_style Способ получения [MYSQLI_NUM]
     *
     * @return mixed
     */
    public function fetch_all($fetch_style = 2)
    {
        switch ($fetch_style) {
            case 1: // MYSQLI_ASSOC
                return $this->_object->fetchAll(PDO::FETCH_ASSOC);
            case 2: // MYSQLI_NUM
                return $this->_object->fetchAll(PDO::FETCH_NUM);
            case 3: // MYSQLI_BOTH
                return $this->_object->fetchAll(PDO::FETCH_BOTH);
        }

        return false;
    }

    /**
     * Возвращает отдельное поле из следующей строки результата
     *
     * @param int $column_number
     *
     * @return string
     */
    public function fetchColumn($column_number = 0)
    {
        return $this->_object->fetchColumn($column_number);
    }

    /**
     * Возвращает следующую строку в виде объекта
     *
     * @param string $class_name Класс
     * @param array $ctor_args Аргументы
     *
     * @return mixed
     */
    public function fetchObject($class_name = 'stdClass', $ctor_args = [])
    {
        return $this->_object->fetchObject($class_name, $ctor_args);
    }

    /**
     * Получение строки результата в виде индексированного массива
     *
     * @return array|null
     */
    public function fetch_row()
    {
        return $this->fetch(PDO::FETCH_NUM);
    }

    /**
     * Получение строки результата в виде ассоциативного массива
     *
     * @return array|null
     */
    public function fetch_assoc()
    {
        return $this->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Получает строку результата в виде индексированного или ассоциативного
     *
     * @param int $result_type
     *
     * @return array|null
     */
    public function fetch_array($result_type = MYSQL_BOTH)
    {
        if ($result_type == MYSQL_NUM) {
            return $this->fetch_row();
        } elseif ($result_type == MYSQL_ASSOC) {
            return $this->fetch_assoc();
        }

        $result = $this->fetch_assoc();

        if ($result === null) {
            return $result;
        }

        return array_merge(array_values($result), $result);
    }

    /**
     * Переходит к следующему результату выполнения для запросов, возвращающих несколько результатов
     *
     * @return bool
     */
    public function nextRowset()
    {
        return $this->_object->nextRowset();
    }

    /**
     * Получение значения параметра запроса
     *
     * @param $attribute
     *
     * @return mixed
     */
    public function getAttribute($attribute)
    {
        return $this->_object->getAttribute($attribute);
    }

    /**
     * Возвращает информацию о заданном поле результата
     *
     * @param $column
     *
     * @return array
     */
    public function getColumnMeta($column)
    {
        return $this->_object->getColumnMeta($column);
    }

    /**
     * Устанавливает значение параметра запрпоса
     *
     * @param string $attribute Параметр
     * @param string $value Значение
     */
    public function setAttribute($attribute, $value)
    {
        $this->_object->setAttribute($attribute, $value);
    }

    /**
     * Устанавливает способ получения результата по умолчанию
     *
     * @link http://www.php.net/manual/en/pdostatement.setfetchmode.php
     *
     * @return mixed
     */
    public function setFetchMode()
    {
        $args = func_get_args();

        return call_user_func_array([$this->_object, 'setFetchMode'], $args);
    }

    /**
     * Связывает переменную с полем
     *
     * @param string $column Поле
     * @param string $param Переменная
     * @param null $type Тип
     *
     * @return bool
     */
    public function bindColumn($column, &$param, $type = null)
    {
        return $this->_object->bindColumn($column, $param, $type);
    }

    /**
     * Связывает переменную с параметром запроса
     *
     * @param string $column Поле
     * @param string $param Параметр
     * @param int $data_type Тип данных
     *
     * @return bool
     */
    public function bindParam($column, &$param, $data_type = PDO::PARAM_STR)
    {
        $this->_binds[$column] = $param;

        return $this->_object->bindParam($column, $param, $data_type);
    }

    /**
     * Устанавливает значение параметра запроса
     *
     * @param string $parameter Параметр
     * @param string $value Значение
     *
     * @return bool
     */
    public function bindValue($parameter, $value)
    {
        $this->_binds[$parameter] = $value;

        return $this->_object->bindValue($parameter, $value);
    }

    /**
     * Закрытие курсора
     *
     * @return bool
     */
    public function close()
    {
        return $this->_object->closeCursor();
    }

    /**
     * Количество строк в результате
     *
     * @return int
     */
    public function rowCount()
    {
        return $this->_object->rowCount();
    }

    /**
     * Количество колонок в результате
     *
     * @return int
     */
    public function columnCount()
    {
        return $this->_object->columnCount();
    }

    /**
     * Выполняет запрос с заданными параметрами
     *
     * @throws Lib_Exception_InvalidArgument
     *
     * @param array $params
     *
     * @return bool
     */
    public function execute(array $params = null)
    {
        if (empty($params)) {
            $res = $this->_object->execute();
        } else {
            $res = $this->_object->execute($params);
        }

        return $res;
    }

    public function __get($name)
    {
        switch ($name) {
            case 'num_rows':
                return $this->_object->rowCount();
            case 'query':
                return $this->_object->queryString;
        }

        return null;
    }
}
