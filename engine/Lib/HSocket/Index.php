<?php

/**
 * Класс индекса в базе
 *
 * @link https://github.com/ahiguti/HandlerSocket-Plugin-for-MySQL/blob/master/docs-en/protocol.en.txt
 * @link http://code.google.com/p/php-handlersocket/
 */
class Lib_HSocket_Index
{
    /**
     * Команды протокола
     */
    public const OP_EQUALS = '='; // операция сравнения 'равно'
    public const OP_GREATER = '>'; // операция сравнения 'больше'
    public const OP_LESSER = '<'; // операция сравнения 'меньше'
    public const OP_EQUALS_GREATER = '>='; // операция сравнения 'больше или равно'
    public const OP_EQUALS_LESSER = '<='; // операция сравнения 'меньше или равно'
    public const OP_INSERT = '+'; // операция добавления новой строки
    public const OP_UPDATE = 'U'; // операция изменения данных
    public const OP_DELETE = 'D'; // операция удаления строки
    public const OP_CREATE = 'P'; // операция создания индекса

    public const UPD_UPDATE = 'U'; // обновить поля
    public const UPD_INCR = '+'; // инкремент полей
    public const UPD_DECR = '-'; // декремент полей
    public const UPD_DELETE = 'D'; // удалить запись

    /** Количество попыток выполнения команды при переподключении */
    public const NATTMPTS_ON_GONE_AWAY_READ = 2;
    public const NATTMPTS_ON_GONE_AWAY_WRITE = 1;

    /**
     * Флаг, индекс открыт для записи
     *
     * @var bool
     */
    private $_isOpenWrite = false;

    /**
     * Флаг, индекс открыт для чтения
     *
     * @var bool
     */
    private $_isOpenRead = false;

    /**
     * Объект сокета
     *
     * @var Lib_HSocket_Socket
     */
    private $_socket = null;

    /**
     * Список полей индекса
     *
     * @var  array of string
     */
    private $_fields = [];

    /**
     * Команда открытия индекса
     *
     * @var array of mixed
     */
    private $_openIndexCmd = null;

    /**
     * Идентификатор индекса
     *
     * @var int
     */
    private $_indexId = -1;

    /**
     * Конструктор класса
     *
     * @throws Lib_Exception_Runtime
     *
     * @param  Lib_HSocket_Socket $socket Объект сокета подключения к базе данных
     * @param  string $table Имя таблицы в которой открыватеся индекс
     * @param  int $indexID Идентификатор индекса
     * @param  string $indexName Имя индекса
     * @param  array $fields Список полей для данного индекса
     */
    public function __construct($socket, $table, $indexID, $indexName, $fields)
    {
        $this->_socket = $socket;
        $this->_indexId = $indexID;
        $this->_fields = $fields;
        $dbName = $socket->getDbName();
        $this->_openIndexCmd = [Lib_HSocket_Index::OP_CREATE, $indexID, $dbName, $table, $indexName, implode(',', $fields)];
    }

    /**
     * Подготовка сокета. Если сокет ещё закрыт, то открыть сокет и создать индекс
     *
     * @param int $sid Идентификатор сокета
     */
    private function _prepareSocket($sid = Lib_HSocket_Socket::S_READ)
    {

        if ($sid == Lib_HSocket_Socket::S_READ && $this->_isOpenRead) {
            return;
        }

        if ($sid == Lib_HSocket_Socket::S_WRITE && $this->_isOpenWrite) {
            return;
        }

        $this->_socket->connect($sid);
        $this->_socket->executeCommand($this->_openIndexCmd, null, $sid);

        if ($sid == Lib_HSocket_Socket::S_WRITE) {
            $this->_isOpenWrite = true;
        } else {
            $this->_isOpenRead = true;
        }
    }

    /**
     * indexID getter
     *
     * @return int
     */
    public function getIndexId()
    {
        return $this->_indexId;
    }

    /**
     * Получает данные из таблицы по нескольким запросам сразу. Эквивалентен UNION в SQL.
     * Выигрыш состоит в том, что все запросы отправляются только одним вызовом socket->write и возвращаются тоже одним socket->read.
     *
     * @param array $requests Массив select-запросов. Каждый запрос состоит из 4 элементов, аналогичных методу Select():
     *        $requests[$i][0]  string $operation    Операция сравнения
     *     $requests[$i][1]  mixed  $indexValues  Значение индексного поля (mixed) или массив со значениями индексных полей (array of mixed), по которым будет производиться выборка
     *        $requests[$i][2]  int    $limit        Максимальное количество возвращаемых строк, по умолчанию = 1
     *     $requests[$i][3]  int    $offset       Количество строк в результате, которое будет пропущено перед тем, как начать возвращать результат, по умолчанию = 0
     *
     * @throws Lib_Exception_Runtime
     * @throws Lib_Exception_InvalidArgument
     *
     * @return array of array  Возвращает массив, где элементами служат возвращённые строки. Каждая строка - ассоциативный массив, где каждый элемент - это пара "имя поля"/"значение поля".
     */
    public function selectMulti($requests)
    {
        $this->_prepareSocket(Lib_HSocket_Socket::S_READ);
        $queries = [];
        $operation = self::OP_EQUALS;

        foreach ($requests as $request) {
            $query = [$this->_indexId, $request[0], count($request[1]), $request[1]];

            if (isset($request[2])) {
                $query[] = $request[2];
            }

            if (isset($request[3])) {
                $query[] = $request[3];
            }

            if (empty($queries)) {
                $operation = $request[0];
            }
            $queries[] = $query;
        }

        if (empty($queries)) {
            throw new Lib_Exception_InvalidArgument("Empty request\n");
        }

        $result = [];
        $nattempt = 1;

        while ($nattempt <= self::NATTMPTS_ON_GONE_AWAY_READ) {
            try {
                $result = $this->_socket->executeMulti($queries, $this->_fields, Lib_HSocket_Socket::S_READ);
                break;
            } catch (Lib_Exception_Runtime $e) {
                if ($nattempt < self::NATTMPTS_ON_GONE_AWAY_READ && $e->getCode() == Lib_HSocket_Socket::ERR_CONNECTION_RESET_BY_PEER) {
                    // connection closed - reconnect
                    $this->_isOpenRead = false;
                    $this->_prepareSocket(Lib_HSocket_Socket::S_READ);
                } else {
                    throw $e;
                } // умираем наглухо
            }
            $nattempt++;
        }

        return $result;
    }

    /**
     * Получает данные из таблицы
     *
     * @param  string $operation Операция сравнения
     * @param  mixed $indexValues Значение индексного поля (mixed) или массив со значениями индексных полей (array of mixed), по которым будет производиться выборка
     *                            Если одно из значений индексных полей представлено массивом значений - будет производиться `WHERE-IN`-выборка по этим значениям
     * @param  int $limit Максимальное количество возвращаемых строк, по умолчанию = 1
     * @param  int $offset Количество строк в результате, которое будет пропущено перед тем, как начать возвращать результат, по умолчанию = 0
     *
     * @throws Lib_Exception_InvalidArgument
     * @throws Lib_Exception_Runtime
     *
     * @return array of array  Возвращает массив, где элементами служат возвращённые строки. Каждая строка - ассоциативный массив, где каждый элемент - это пара "имя поля"/"значение поля".
     */
    public function select($operation, $indexValues, $limit = 1, $offset = 0)
    {
        $in = null;
        $icol = 0;

        if (is_array($indexValues)) {
            foreach ($indexValues as $k => $indexValue) {
                if (!is_array($indexValue)) {
                    continue;
                }

                if ($in !== null) {
                    throw new \Lib_Exception_InvalidArgument('`WHERE-IN` query may have only one index column in expression');
                }
                $in = $indexValue;
                $indexValues[$k] = 0;
                $icol = $k;
            }
        }

        $this->_prepareSocket(Lib_HSocket_Socket::S_READ);

        $query = [$this->_indexId, $operation, is_array($indexValues) ? count($indexValues) : 1, $indexValues, $limit, $offset];

        if ($in !== null) {
            $query[] = '@';
            $query[] = $icol;
            $query[] = count($in);

            foreach ($in as $iv) {
                $query[] = $iv;
            }
        }

        $result = [];
        $nattempt = 1;

        while ($nattempt <= self::NATTMPTS_ON_GONE_AWAY_READ) {
            try {
                $result = $this->_socket->executeCommand(
                    $query,
                    $this->_fields,
                    Lib_HSocket_Socket::S_READ
                );
                break;
            } catch (Lib_Exception_Runtime $e) {
                if ($nattempt < self::NATTMPTS_ON_GONE_AWAY_READ && $e->getCode() == Lib_HSocket_Socket::ERR_CONNECTION_RESET_BY_PEER) {
                    // connection closed - reconnect
                    $this->_isOpenRead = false;
                    $this->_prepareSocket(Lib_HSocket_Socket::S_READ);
                } else {
                    throw $e; // умираем наглухо
                }
            }
            $nattempt++;
        }

        return $result;
    }

    /**
     * Получает одну строку из таблицы (первую, соответствующую условиям)
     *
     * @param  string $operation Операция сравнения
     * @param  mixed $indexValues Значение индексного поля (mixed) или массив со значениями индексных полей (array of mixed), по которым будет производиться выборка
     *
     * @throws Lib_Exception_Runtime
     *
     * @return  bool|array          Возвращает ассоциативный массив, где каждый элемент - это пара "имя поля"/"значение поля".
     * @exception  Lib_Exception_Runtime
     */
    public function selectRow($operation, $indexValues)
    {
        $this->_prepareSocket(Lib_HSocket_Socket::S_READ);

        $result = [];
        $nattempt = 1;

        while ($nattempt <= self::NATTMPTS_ON_GONE_AWAY_READ) {
            try {
                $result = $this->_socket->executeCommand([$this->_indexId, $operation, is_array($indexValues) ? count($indexValues) : 1, $indexValues, 1, 0], $this->_fields, Lib_HSocket_Socket::S_READ);

                if (count($result) == 0) {
                    return false;
                }
                break;
            } catch (Lib_Exception_Runtime $e) {
                if ($nattempt < self::NATTMPTS_ON_GONE_AWAY_READ && $e->getCode() == Lib_HSocket_Socket::ERR_CONNECTION_RESET_BY_PEER) {
                    // connection closed - reconnect
                    $this->_isOpenRead = false;
                    $this->_prepareSocket(Lib_HSocket_Socket::S_READ);
                } else {
                    throw $e; // умираем наглухо
                }
            }
            $nattempt++;
        }

        return $result[0];
    }

    /**
     * Вставляет данные в таблицу
     *
     * @param  array $dataValues Массив со значениями полей новой строки
     *
     * @throws Lib_Exception_Runtime
     *
     * @return int|null
     */
    public function insert($dataValues)
    {
        $this->_prepareSocket(Lib_HSocket_Socket::S_WRITE);

        $nattempt = 1;
        $result = [];

        while ($nattempt <= self::NATTMPTS_ON_GONE_AWAY_READ) {
            try {
                $result = $this->_socket->executeCommand([$this->_indexId, self::OP_INSERT, is_array($dataValues) ? count($dataValues) : 1, $dataValues], null, Lib_HSocket_Socket::S_WRITE);
                break;
            } catch (Lib_Exception_Runtime $e) {
                if ($nattempt < self::NATTMPTS_ON_GONE_AWAY_WRITE && $e->getCode() == Lib_HSocket_Socket::ERR_CONNECTION_RESET_BY_PEER) {
                    // connection closed - reconnect
                    $this->_isOpenWrite = false;
                    $this->_prepareSocket(Lib_HSocket_Socket::S_WRITE);
                } else {
                    throw $e; // умираем наглухо
                }
            }
            $nattempt++;
        }

        return $result[0][0] ?? null;
    }

    /**
     * Изменяет данные в таблице
     *
     * @param  string $operation Операция сравнения
     * @param  mixed $indexValues Значение индексного поля (mixed) или массив со значениями индексных полей (array of mixed), по которым будет производиться выборка
     * @param  array $dataValues Массив с новыми значениями полей строки
     * @param int $limit Максимальное количество изменяемых строк, по умолчанию = 1
     * @param int $offset Количество строк, которое будет пропущено перед тем, как начать вносить изменения, по умолчанию = 0
     * @param string $updateOperation Операция обновления
     *
     * @throws Lib_Exception_Runtime
     *
     * @return int                            Возвращает количество изменённых строк
     */
    public function update($operation, $indexValues, $dataValues, $limit = 1, $offset = 0, $updateOperation = self::UPD_UPDATE)
    {
        $this->_prepareSocket(Lib_HSocket_Socket::S_WRITE);

        $result = [];
        $nattempt = 1;

        while ($nattempt <= self::NATTMPTS_ON_GONE_AWAY_READ) {
            try {
                $result = $this->_socket->executeCommand([$this->_indexId, $operation, is_array($indexValues) ? count($indexValues) : 1, $indexValues, $limit, $offset, $updateOperation, $dataValues], null, Lib_HSocket_Socket::S_WRITE);
                break;
            } catch (Lib_Exception_Runtime $e) {
                if ($nattempt < self::NATTMPTS_ON_GONE_AWAY_WRITE && $e->getCode() == Lib_HSocket_Socket::ERR_CONNECTION_RESET_BY_PEER) {
                    // connection closed - reconnect
                    $this->_isOpenWrite = false;
                    $this->_prepareSocket(Lib_HSocket_Socket::S_WRITE);
                } else {
                    throw $e; // умираем наглухо
                }
            }
            $nattempt++;
        }

        return $result[0][0];
    }

    /**
     * Изменяет данные в таблице
     *
     * @param  string $operation Операция сравнения
     * @param  mixed $indexValues Значение индексного поля (mixed) или массив со значениями индексных полей (array of mixed), по которым будет производиться выборка
     * @param  array $dataValues Массив с новыми значениями полей строки
     * @param int $limit Максимальное количество изменяемых строк, по умолчанию = 1
     * @param int $offset Количество строк, которое будет пропущено перед тем, как начать вносить изменения, по умолчанию = 0
     * @param string $updateOperation Операция обновления
     *
     * @throws Lib_Exception_Runtime
     *
     * @return int                            Возвращает запись до изменения
     */
    public function selectAndUpdate($operation, $indexValues, $dataValues, $limit = 1, $offset = 0, $updateOperation = self::UPD_UPDATE)
    {
        $this->_prepareSocket(Lib_HSocket_Socket::S_WRITE);

        $result = [];
        $nattempt = 1;

        while ($nattempt <= self::NATTMPTS_ON_GONE_AWAY_READ) {
            try {
                $result = $this->_socket->executeCommand([$this->_indexId, $operation, is_array($indexValues) ? count($indexValues) : 1, $indexValues, $limit, $offset, $updateOperation . '?', $dataValues], null, Lib_HSocket_Socket::S_WRITE);
                break;
            } catch (Lib_Exception_Runtime $e) {
                if ($nattempt < self::NATTMPTS_ON_GONE_AWAY_WRITE && $e->getCode() == Lib_HSocket_Socket::ERR_CONNECTION_RESET_BY_PEER) {
                    // connection closed - reconnect
                    $this->_isOpenWrite = false;
                    $this->_prepareSocket(Lib_HSocket_Socket::S_WRITE);
                } else {
                    throw $e; // умираем наглухо
                }
            }
            $nattempt++;
        }

        return $result;
    }

    /**
     * Удаляет данные из таблицы
     *
     * @param  string $operation Операция сравнения
     * @param  mixed $indexValues Значение индексного поля (mixed) или массив со значениями индексных полей (array of mixed), по которым будет производиться выборка
     * @param int $limit Максимальное количество удаляемых строк, по умолчанию = 1
     * @param int $offset Количество строк, которое будет пропущено перед тем, как начать удаление, по умолчанию = 0
     *
     * @throws Lib_Exception_Runtime
     *
     * @return int                        Возвращает количество удалённых строк
     */
    public function delete($operation, $indexValues, $limit = 1, $offset = 0)
    {
        $this->_prepareSocket(Lib_HSocket_Socket::S_WRITE);

        $result = [];
        $nattempt = 1;

        while ($nattempt <= self::NATTMPTS_ON_GONE_AWAY_READ) {
            try {
                $result = $this->_socket->executeCommand([$this->_indexId, $operation, is_array($indexValues) ? count($indexValues) : 1, $indexValues, $limit, $offset, self::OP_DELETE], null, Lib_HSocket_Socket::S_WRITE);
                break;
            } catch (Lib_Exception_Runtime $e) {
                if ($nattempt < self::NATTMPTS_ON_GONE_AWAY_WRITE && $e->getCode() == Lib_HSocket_Socket::ERR_CONNECTION_RESET_BY_PEER) {
                    // connection closed - reconnect
                    $this->_isOpenWrite = false;
                    $this->_prepareSocket(Lib_HSocket_Socket::S_WRITE);
                } else {
                    throw $e; // умираем наглухо
                }
            }
            $nattempt++;
        }

        return $result[0][0];
    }
}
