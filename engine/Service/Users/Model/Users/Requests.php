<?php

namespace Service\Users;

/**
 * @property Model_Factory $factory
 */
class Model_Users_Requests extends \Lib_ORM
{
    public const TABLE = 'users_requests';

    public const INDEX = 'PRIMARY';
    public const INDEX_USERID = 'i_userId';

    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $request = new Model_Users_Requests_Request($this);

        return $request;
    }

    /**
     * @param $requestId
     * @param bool $for_save
     *
     * @return Model_Users_Requests_Request|null
     */
    public function getById($requestId, $for_save = false)
    {
        $request = new Model_Users_Requests_Request($this);

        if (parent::_getOneByIndex($requestId, $request, new \Database_Main(), self::TABLE, self::INDEX,
            $for_save ? true : false)) {
            return $request;
        }

        return null;
    }

    /**
     * @param $userId
     * @param bool $for_save
     *
     * @return Model_Users_Requests_Request[]|null
     */
    public function getByUserId($userId, $for_save = false)
    {
        $request = new Model_Users_Requests_Request($this);

        return parent::_getCollectionByIndex($userId, $request, new \Database_Main(), self::TABLE, self::INDEX_USERID,
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
     * @param Model_Users_Requests_Request $request
     *
     * @return bool|int|null
     */
    public function save(Model_Users_Requests_Request $request)
    {
        if ($request->requestId) {
            $result = parent::_saveDifferencesByIndex($request->requestId, $request, new \Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($request, new \Database_Main(), self::TABLE, self::INDEX);
            $request->requestId = $result;
        }

        return $result;
    }

    public function delete(Model_Users_Requests_Request $request)
    {
        return parent::_deleteByIndex($request->requestId, new \Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new Model_Users_Requests_Request($this), new \Database_Main(), self::TABLE);

        return $query;
    }
}
