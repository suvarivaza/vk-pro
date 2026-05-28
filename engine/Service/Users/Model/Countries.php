<?php

namespace Service\Users;

/**
 * @property Model_Factory $factory
 */
class Model_Countries extends \Lib_ORM
{
    public const TABLE = 'countries';

    public const INDEX_CITYID = 'i_countryId';
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
        $country = new Model_Countries_Country($this);
        $country->isNew = true;

        return $country;
    }

    /**
     * @param $countryId
     * @param bool $for_save
     *
     * @return null|  Model_Countries_Country
     */
    public function getById($countryId, $for_save = false)
    {
        $country = new   Model_Countries_Country($this);

        if (!parent::_getOneByIndex($countryId, $country, new \Database_Main(), self::TABLE, self::INDEX_CITYID,
            $for_save)) {
            return null;
        }

        return $country;
    }

    /**
     * @param bool $for_save
     *
     * @return Model_Countries_Country[]
     */
    public function getListByCount($for_save = false, $limit = 1000)
    {
        $country = new   Model_Countries_Country($this);
        $list = parent::_getCollectionAllByIndex($country, new \Database_Main(), self::TABLE, self::INDEX_COUNT,
            $for_save, $limit);

        foreach ($list as $id => $city) {
            if (!$city->isVisible) {
                unset($list[$id]);
            }
        }

        return $list;
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
     * @param Model_Countries_Country $country
     *
     * @return bool|int|null
     */
    public function save(Model_Countries_Country $country)
    {
        if (!$country->isNew) {
            $result = parent::_saveDifferencesByIndex($country->countryId, $country, new \Database_Main(), self::TABLE,
                self::INDEX_CITYID);
        } else {
            $result = parent::_insert($country, new \Database_Main(), self::TABLE, self::INDEX_CITYID);
            $country->isNew = false;
        }

        return $result;
    }

    public function delete(Model_Countries_Country $country)
    {
        return parent::_deleteByIndex($country->countryId, new \Database_Main(), self::TABLE, self::INDEX_CITYID);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new   Model_Countries_Country($this), new \Database_Main(), self::TABLE);

        return $query;
    }
}
