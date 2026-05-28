<?php

namespace Service\Users;

/**
 * @property Model_Factory $factory
 */
class Model_Users_Balances extends \Lib_ORM
{
    public const INDEX = 'PRIMARY';
    public const INDEX_USERID = 'i_userId';
    public const INDEX_USERID_ISBONUS = 'i_userId_isBonus';
    public const INDEX_USERID_ISCOMP = 'i_userId_isCompensation';
    public const INDEX_USERID_ISPENALTY = 'i_userId_isPenalty';
    public const INDEX_USERID_ISTASK = 'i_userId_isTask';
    private static $TABLE = 'users_balance_';
    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory, $date)
    {
        if (!$date) {
            $date = time();
        }
        self::$TABLE .= date('Y_m', $date);

        $this->_factory = $factory;
    }

    public function setDate($time)
    {
        self::$TABLE = 'users_balance_' . date('Y_m', $time);
    }

    public function getNew()
    {
        $balance = new Model_Users_Balances_Balance($this);
        $balance->isBonus = false;
        $balance->isPenalty = false;
        $balance->isTask = false;
        $balance->isBot = false;
        $balance->isCompensation = false;
        $balance->isReferrer = false;

        return $balance;
    }

    /**
     * @param $balanceId
     * @param bool $for_save
     *
     * @return Model_Users_Balances_Balance|null
     */
    public function getById($balanceId, $for_save = false)
    {
        $balance = new Model_Users_Balances_Balance($this);

        if (parent::_getOneByIndex($balanceId, $balance, new \Database_Main(), self::$TABLE, self::INDEX,
            $for_save ? true : false)) {
            return $balance;
        }

        return null;
    }

    /**
     * @param $userId
     * @param bool $for_save
     *
     * @return Model_Users_Balances_Balance[]
     */
    public function getByUserId($userId, $for_save = false)
    {
        $balance = new Model_Users_Balances_Balance($this);

        return parent::_getCollectionByIndex($userId, $balance, new \Database_Main(), self::$TABLE, self::INDEX_USERID,
            $for_save ? true : false);
    }

    /**
     * @param $userId
     * @param $isBonus
     * @param bool $for_save
     *
     * @return Model_Users_Balances_Balance[]
     */
    public function getByUserIdIsBonus($userId, $isBonus, $for_save = false)
    {
        $balance = new Model_Users_Balances_Balance($this);

        return parent::_getCollectionByIndex([$userId, $isBonus], $balance, new \Database_Main(), self::$TABLE,
            self::INDEX_USERID_ISBONUS, $for_save ? true : false);
    }

    /**
     * @param $userId
     * @param $isCompensation
     * @param bool $for_save
     *
     * @return Model_Users_Balances_Balance[]
     */
    public function getByUserIdIsCompensation($userId, $isCompensation, $for_save = false)
    {
        $balance = new Model_Users_Balances_Balance($this);

        return parent::_getCollectionByIndex([$userId, $isCompensation], $balance, new \Database_Main(), self::$TABLE,
            self::INDEX_USERID_ISCOMP, $for_save ? true : false);
    }

    /**
     * @param $userId
     * @param $isPenalty
     * @param bool $for_save
     *
     * @return Model_Users_Balances_Balance[]
     */
    public function getByUserIdIsPenalty($userId, $isPenalty, $for_save = false)
    {
        $balance = new Model_Users_Balances_Balance($this);

        return parent::_getCollectionByIndex([$userId, $isPenalty], $balance, new \Database_Main(), self::$TABLE,
            self::INDEX_USERID_ISPENALTY, $for_save ? true : false);
    }

    /**
     * @param $userId
     * @param $isTask
     * @param bool $for_save
     *
     * @return Model_Users_Balances_Balance[]
     */
    public function getByUserIdIsTask($userId, $isTask, $for_save = false)
    {
        $balance = new Model_Users_Balances_Balance($this);

        return parent::_getCollectionByIndex([$userId, $isTask], $balance, new \Database_Main(), self::$TABLE,
            self::INDEX_USERID_ISTASK, $for_save ? true : false);
    }

    public function __get($name)
    {
        switch ($name) {
            case 'factory':
                return $this->_factory;
        }

        throw new \Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }

    /**
     * @param Model_Users_Balances_Balance $balance
     *
     * @return bool|int|null
     */
    public function save(Model_Users_Balances_Balance $balance)
    {
        if ($balance->balanceId) {
            $result = parent::_saveDifferencesByIndex($balance->balanceId, $balance, new \Database_Main(), self::$TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($balance, new \Database_Main(), self::$TABLE, self::INDEX);
            $balance->balanceId = $result;
        }

        return $result;
    }

    public function delete(Model_Users_Balances_Balance $balance)
    {
        return parent::_deleteByIndex($balance->balanceId, new \Database_Main(), self::$TABLE, self::INDEX);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new Model_Users_Balances_Balance($this), new \Database_Main(), self::$TABLE);

        return $query;
    }
}
