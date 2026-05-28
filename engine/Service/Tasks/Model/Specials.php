<?php

namespace Service\Tasks;

/**
 * @property Model_Factory $factory
 */
class Model_Specials extends \Lib_ORM
{
    public const TABLE = 'specials';

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
        $special = new Model_Specials_Special($this);
        $special->dateCreate = time();

        return $special;
    }

    /**
     * @param specialId
     * @param bool $for_save
     *
     * @return null| Model_Specials_Special
     */
    public function getById($specialId, $for_save = false)
    {
        $special = new  Model_Specials_Special($this);

        if (!parent::_getOneByIndex($specialId, $special, new \Database_Main(), self::TABLE, self::INDEX, $for_save)) {
            return null;
        }

        return $special;
    }

    /**
     * @param $userId
     * @param bool $for_save
     *
     * @return Model_Specials_Special
     */
    public function getByUserId($userId, $for_save = false)
    {
        $special = new  Model_Specials_Special($this);

        if (!parent::_getOneByIndex($userId, $special, new \Database_Main(), self::TABLE, self::INDEX_USERID,
            $for_save)) {
            return null;
        }

        return $special;
    }

    /**
     * @param $userId
     * @param $isActive
     * @param bool $for_save
     *
     * @return Model_Specials_Special|null
     */
    public function getByUserIdIsActive($userId, $isActive, $for_save = false)
    {
        $special = new Model_Specials_Special($this);

        if (!parent::_getOneByIndex([$userId, $isActive], $special, new \Database_Main(), self::TABLE,
            self::INDEX_USERID_ISACTIVE, $for_save)) {
            return null;
        }

        return $special;
    }

    /**
     * @param Model_Specials_Special $special
     *
     * @return bool|int|null
     */
    public function save(Model_Specials_Special $special)
    {
        if ($special->specialId) {
            $result = parent::_saveDifferencesByIndex($special->specialId, $special, new \Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($special, new \Database_Main(), self::TABLE, self::INDEX);
            $special->specialId = $result;
        }

        return $result;
    }

    public function delete(Model_Specials_Special $special)
    {
        return parent::_deleteByIndex($special->specialId, new \Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new  Model_Specials_Special($this), new \Database_Main(), self::TABLE);

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
