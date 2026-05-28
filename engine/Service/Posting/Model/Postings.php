<?php

namespace Service\Posting;

/**
 * @property Model_Factory $factory
 */
class Model_Postings extends \Lib_ORM
{
    public const TABLE = 'posting';

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
        $posting = new Model_Postings_Posting($this);
        $posting->dateCreate = time();

        return $posting;
    }

    /**
     * @param postingId
     * @param bool $for_save
     *
     * @return null| Model_Postings_Posting
     */
    public function getById($postingId, $for_save = false)
    {
        $posting = new  Model_Postings_Posting($this);

        if (!parent::_getOneByIndex($postingId, $posting, new \Database_Main(), self::TABLE, self::INDEX, $for_save)) {
            return null;
        }

        return $posting;
    }

    /**
     * @param $userId
     * @param bool $for_save
     *
     * @return Model_Postings_Posting
     */
    public function getByUserId($userId, $for_save = false)
    {
        $posting = new  Model_Postings_Posting($this);

        if (!parent::_getOneByIndex($userId, $posting, new \Database_Main(), self::TABLE, self::INDEX_USERID,
            $for_save)) {
            return null;
        }

        return $posting;
    }

    /**
     * @param $userId
     * @param $isActive
     * @param bool $for_save
     *
     * @return Model_Postings_Posting|null
     */
    public function getByUserIdIsActive($userId, $isActive, $for_save = false)
    {
        $posting = new Model_Postings_Posting($this);

        if (!parent::_getOneByIndex([$userId, $isActive], $posting, new \Database_Main(), self::TABLE,
            self::INDEX_USERID_ISACTIVE, $for_save)) {
            return null;
        }

        return $posting;
    }

    /**
     * @param Model_Postings_Posting $posting
     *
     * @return bool|int|null
     */
    public function save(Model_Postings_Posting $posting)
    {
        if ($posting->postingId) {
            $result = parent::_saveDifferencesByIndex($posting->postingId, $posting, new \Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($posting, new \Database_Main(), self::TABLE, self::INDEX);
            $posting->postingId = $result;
        }

        return $result;
    }

    public function delete(Model_Postings_Posting $posting)
    {
        return parent::_deleteByIndex($posting->postingId, new \Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new  Model_Postings_Posting($this), new \Database_Main(), self::TABLE);

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
