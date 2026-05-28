<?php

namespace Service\Users;

/**
 * @property Model_Factory $factory
 */
class Model_Cities extends \Lib_ORM
{
    public const TABLE = 'cities';

    public const INDEX_CITYID = 'i_cityId';
    public const INDEX_COUNT = 'i_count';

    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $city = new Model_Cities_City($this);
        $city->isNew = true;

        return $city;
    }

    /**
     * @param $cityId
     * @param bool $for_save
     *
     * @return null|  Model_Cities_City
     */
    public function getById($cityId, $for_save = false)
    {
        $city = new   Model_Cities_City($this);

        if (!parent::_getOneByIndex($cityId, $city, new \Database_Main(), self::TABLE, self::INDEX_CITYID, $for_save)) {
            return null;
        }

        return $city;
    }

    /**
     * @param bool $for_save
     *
     * @return Model_Cities_City[]
     */
    public function getListByCount($for_save = false, $limit = 1000)
    {
        $query = $this->query();
        $query->sort('count', 'DESC');
        $query->filter->fieldValue('isVisible', '=', true);

        if ($for_save) {
            $it = $query->iteratorForSave();
        } else {
            $it = $query->iterator();
        }
        $list = [];

        foreach ($it as $city) {
            $list[] = $city;
        }

        return $list;
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new   Model_Cities_City($this), new \Database_Main(), self::TABLE);

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

    /**
     * @param Model_Cities_City $task
     *
     * @return bool|int|null
     */
    public function save(Model_Cities_City $city)
    {
        if (!$city->isNew) {
            $result = parent::_saveDifferencesByIndex($city->cityId, $city, new \Database_Main(), self::TABLE,
                self::INDEX_CITYID);
        } else {
            $result = parent::_insert($city, new \Database_Main(), self::TABLE, self::INDEX_CITYID);
            $city->isNew = false;
        }

        return $result;
    }

    public function delete(Model_Cities_City $city)
    {
        return parent::_deleteByIndex($city->cityId, new \Database_Main(), self::TABLE, self::INDEX_CITYID);
    }
}
