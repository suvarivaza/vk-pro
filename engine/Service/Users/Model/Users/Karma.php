<?php

namespace Service\Users;

/**
 * @property Model_Factory $factory
 */
class Model_Users_Karma extends \Lib_ORM
{
    public const INDEX = 'PRIMARY';
    public const INDEX_USERID = 'i_userId';
    private static $TABLE = 'users_karma_';
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
        self::$TABLE = 'users_karma_' . date('Y_m', $time);
    }

    public function getNew()
    {
        $karma = new Model_Users_Karma_Karma($this);
        $karma->taskId = 0;
        $karma->isBot = false;

        return $karma;
    }

    /**
     * @param $karmaId
     * @param bool $for_save
     *
     * @return Model_Users_Karma_Karma|null
     */
    public function getById($karmaId, $for_save = false)
    {
        $karma = new Model_Users_Karma_Karma($this);

        if (parent::_getOneByIndex($karmaId, $karma, new \Database_Main(), self::$TABLE, self::INDEX,
            $for_save ? true : false)) {
            return $karma;
        }

        return null;
    }

    /**
     * @param $userId
     * @param bool $for_save
     *
     * @return Model_Users_Karma_Karma|null
     */
    public function getByUserId($userId, $for_save = false)
    {
        $karma = new Model_Users_Karma_Karma($this);

        return parent::_getCollectionByIndex($userId, $karma, new \Database_Main(), self::$TABLE, self::INDEX_USERID,
            $for_save ? true : false);
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
     * @param Model_Users_User $user
     *
     * @return bool|int|null
     */
    public function save(Model_Users_Karma_Karma $karma)
    {
        if ($karma->karmaId) {
            $result = parent::_saveDifferencesByIndex($karma->karmaId, $karma, new \Database_Main(), self::$TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($karma, new \Database_Main(), self::$TABLE, self::INDEX);
            $karma->karmaId = $result;
        }

        return $result;
    }

    public function delete(Model_Users_Karma_Karma $karma)
    {
        return parent::_deleteByIndex($karma->karmaId, new \Database_Main(), self::$TABLE, self::INDEX);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new Model_Users_Karma_Karma($this), new \Database_Main(), self::$TABLE);

        return $query;
    }
}
