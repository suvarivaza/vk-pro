<?php

namespace Service\Auto;

use Database_Main;
use Lib_Exception_UnknownProperty_Backtraced;
use Lib_ORM;
use Lib_ORM_Query;

/**
 * @property Model_Factory $factory
 * @property Model_Autos_Groups $groups
 * @property Model_Autos_Templates $templates
 */
class Model_Autos extends Lib_ORM
{
    public const TABLE = 'auto';

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
        $auto = new Model_Autos_Auto($this);
        $auto->dateCreate = time();

        return $auto;
    }

    /**
     * @param autoId
     * @param bool $for_save
     *
     * @return null| Model_Autos_Auto
     */
    public function getById($autoId, $for_save = false)
    {
        $auto = new  Model_Autos_Auto($this);

        if (!parent::_getOneByIndex($autoId, $auto, new Database_Main(), self::TABLE, self::INDEX, $for_save)) {
            return null;
        }

        return $auto;
    }

    /**
     * @param $userId
     * @param bool $for_save
     *
     * @return Model_Autos_Auto
     */
    public function getByUserId($userId, $for_save = false)
    {
        $auto = new  Model_Autos_Auto($this);

        if (!parent::_getOneByIndex($userId, $auto, new Database_Main(), self::TABLE, self::INDEX_USERID, $for_save)) {
            return null;
        }

        return $auto;
    }

    /**
     * @param $userId
     * @param $isActive
     * @param bool $for_save
     *
     * @return Model_Autos_Auto|null
     */
    public function getByUserIdIsActive($userId, $isActive, $for_save = false)
    {
        $auto = new Model_Autos_Auto($this);

        if (!parent::_getOneByIndex([$userId, $isActive], $auto, new Database_Main(), self::TABLE,
            self::INDEX_USERID_ISACTIVE, $for_save)) {
            return null;
        }

        return $auto;
    }

    /**
     * @param Model_Autos_Auto $auto
     *
     * @return bool|int|null
     */
    public function save(Model_Autos_Auto $auto)
    {
        if ($auto->autoId) {
            $result = parent::_saveDifferencesByIndex($auto->autoId, $auto, new Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($auto, new Database_Main(), self::TABLE, self::INDEX);
            $auto->autoId = $result;
        }

        return $result;
    }

    public function delete(Model_Autos_Auto $auto)
    {
        return parent::_deleteByIndex($auto->autoId, new Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return Lib_ORM_Query
     */
    public function query()
    {
        $query = new Lib_ORM_Query(new  Model_Autos_Auto($this), new Database_Main(), self::TABLE);

        return $query;
    }

    public function __get($name)
    {
        switch ($name) {
            case 'factory':
                return $this->_factory;
            case 'groups':
                if ($this->_groups === null) {
                    $this->_groups = new Model_Autos_Groups($this->factory);
                }

                return $this->_groups;
            case 'templates':
                if ($this->_templates === null) {
                    $this->_templates = new Model_Autos_Templates($this->factory);
                }

                return $this->_templates;
        }

        throw new Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }
}
