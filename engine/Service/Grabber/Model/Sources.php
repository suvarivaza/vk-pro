<?php

namespace Service\Grabber;

/**
 * @property Model_Factory $factory
 */
class Model_Sources extends \Lib_ORM
{
    public const TABLE = 'grabber_sources';

    public const INDEX = 'PRIMARY';
    public const INDEX_USERID = 'i_userId';
    public const INDEX_GRABBERID = 'i_grabberId';
    public const INDEX_GROUPID = 'i_groupId';

    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $source = new Model_Sources_Source($this);
        $source->dateCreate = time();
        $source->itemId = '';
        $source->interval = 0;
        $source->linkDelete = false;
        $source->hashtags = '';
        $source->count = 0;
        $source->countAll = 0;
        $source->notAdv = true;

        return $source;
    }

    /**
     * @param $sourceId
     * @param bool $for_save
     *
     * @return null| Model_Sources_Source
     */
    public function getById($sourceId, $for_save = false)
    {
        $source = new  Model_Sources_Source($this);

        if (!parent::_getOneByIndex($sourceId, $source, new \Database_Main(), self::TABLE, self::INDEX, $for_save)) {
            return null;
        }

        return $source;
    }

    /**
     * @param $userId
     * @param bool $for_save
     *
     * @return Model_Sources_Source[]
     */
    public function getByUserId($userId, $for_save = false)
    {
        $source = new  Model_Sources_Source($this);

        return parent::_getCollectionByIndex($userId, $source, new \Database_Main(), self::TABLE, self::INDEX_USERID,
            $for_save);
    }

    /**
     * @param $groupId
     * @param bool $for_save
     *
     * @return Model_Sources_Source[]
     */
    public function getByGroupId($groupId, $for_save = false)
    {
        $source = new  Model_Sources_Source($this);

        return parent::_getCollectionByIndex($groupId, $source, new \Database_Main(), self::TABLE, self::INDEX_GROUPID,
            $for_save);
    }

    /**
     * @param $grabberId
     * @param bool $for_save
     *
     * @return Model_Sources_Source[]
     */
    public function getByGrabberId($grabberId, $for_save = false)
    {
        $source = new  Model_Sources_Source($this);

        return parent::_getCollectionByIndex($grabberId, $source, new \Database_Main(), self::TABLE,
            self::INDEX_GRABBERID, $for_save);
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
     * @param Model_Sources_Source $source
     *
     * @return bool|int|null
     */
    public function save(Model_Sources_Source $source)
    {
        if ($source->sourceId) {
            $result = parent::_saveDifferencesByIndex($source->sourceId, $source, new \Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($source, new \Database_Main(), self::TABLE, self::INDEX);
            $source->sourceId = $result;
        }

        return $result;
    }

    public function delete(Model_Sources_Source $source)
    {
        return parent::_deleteByIndex($source->sourceId, new \Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new  Model_Sources_Source($this), new \Database_Main(), self::TABLE);

        return $query;
    }
}
