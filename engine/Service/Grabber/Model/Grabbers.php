<?php

namespace Service\Grabber;

/**
 * @property Model_Factory $factory
 */
class Model_Grabbers extends \Lib_ORM
{
    public const TABLE = 'grabber';

    public const INDEX = 'PRIMARY';
    public const INDEX_USERID = 'i_userId';
    public const INDEX_USERID_ISACTIVE = 'i_userId_isActive';

    /** @var Model_Factory */
    protected $_factory;

    private $_groups = null;
    private $_templates = null;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $grabber = new Model_Grabbers_Grabber($this);
        $grabber->dateCreate = time();

        return $grabber;
    }

    /**
     * @param grabberId
     * @param bool $for_save
     *
     * @return null| Model_Grabbers_Grabber
     */
    public function getById($grabberId, $for_save = false)
    {
        $grabber = new  Model_Grabbers_Grabber($this);

        if (!parent::_getOneByIndex($grabberId, $grabber, new \Database_Main(), self::TABLE, self::INDEX, $for_save)) {
            return null;
        }

        return $grabber;
    }

    /**
     * @param $userId
     * @param bool $for_save
     *
     * @return Model_Grabbers_Grabber
     */
    public function getByUserId($userId, $for_save = false)
    {
        $grabber = new  Model_Grabbers_Grabber($this);

        if (!parent::_getOneByIndex($userId, $grabber, new \Database_Main(), self::TABLE, self::INDEX_USERID,
            $for_save)) {
            return null;
        }

        return $grabber;
    }

    /**
     * @param $userId
     * @param $isActive
     * @param bool $for_save
     *
     * @return Model_Grabbers_Grabber|null
     */
    public function getByUserIdIsActive($userId, $isActive, $for_save = false)
    {
        $grabber = new Model_Grabbers_Grabber($this);

        if (!parent::_getOneByIndex([$userId, $isActive], $grabber, new \Database_Main(), self::TABLE,
            self::INDEX_USERID_ISACTIVE, $for_save)) {
            return null;
        }

        return $grabber;
    }

    /**
     * @param Model_Grabbers_Grabber $grabber
     *
     * @return bool|int|null
     */
    public function save(Model_Grabbers_Grabber $grabber)
    {
        if ($grabber->grabberId) {
            $result = parent::_saveDifferencesByIndex($grabber->grabberId, $grabber, new \Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($grabber, new \Database_Main(), self::TABLE, self::INDEX);
            $grabber->grabberId = $result;
        }

        return $result;
    }

    public function delete(Model_Grabbers_Grabber $grabber)
    {
        return parent::_deleteByIndex($grabber->grabberId, new \Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new  Model_Grabbers_Grabber($this), new \Database_Main(), self::TABLE);

        return $query;
    }

    public function __get($name)
    {
        switch ($name) {
            case 'factory':
                return $this->_factory;
        }

        throw new \Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }
}
