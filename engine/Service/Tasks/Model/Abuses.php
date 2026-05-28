<?php

namespace Service\Tasks;

/**
 * @property Model_Factory $factory
 */
class Model_Abuses extends \Lib_ORM
{
    public const TABLE = 'abuse';

    public const INDEX = 'PRIMARY';
    public const INDEX_TASKID = 'i_taskId';
    public const INDEX_USERID = 'i_userId';
    public const INDEX_TASKID_USERID = 'i_taskId_userId';

    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $abuse = new Model_Abuses_Abuse($this);
        $abuse->isDone = false;
        $abuse->comment = '';

        return $abuse;
    }

    /**
     * @param $abuseId
     * @param bool $for_save
     *
     * @return null| Model_Abuses_Abuse
     */
    public function getById($abuseId, $for_save = false)
    {
        $abuse = new  Model_Abuses_Abuse($this);

        if (!parent::_getOneByIndex($abuseId, $abuse, new \Database_Main(), self::TABLE, self::INDEX, $for_save)) {
            return null;
        }

        return $abuse;
    }

    /**
     * @param $taskId
     * @param $userId
     * @param bool $for_save
     *
     * @return Model_Abuses_Abuse|null
     */
    public function getByTaskIdUserId($taskId, $userId, $for_save = false)
    {
        $abuse = new  Model_Abuses_Abuse($this);

        if (!parent::_getOneByIndex([$taskId, $userId], $abuse, new \Database_Main(), self::TABLE,
            self::INDEX_TASKID_USERID, $for_save)) {
            return null;
        }

        return $abuse;
    }

    /**
     * @param $taskId
     * @param false $for_save
     * @return \Lib_ORM_Object[]
     * @throws \Lib_Exception_Logic_Backtraced
     */
    public function getByTaskId($taskId, $for_save = false)
    {
        $abuse = new  Model_Abuses_Abuse($this);

        return parent::_getCollectionByIndex($taskId, $abuse, new \Database_Main(), self::TABLE, self::INDEX_TASKID,
            $for_save);
    }

    public function getByUserId($userId, $for_save = false)
    {
        $abuse = new  Model_Abuses_Abuse($this);

        return parent::_getCollectionByIndex($userId, $abuse, new \Database_Main(), self::TABLE, self::INDEX_USERID,
            $for_save);
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
     * @param Model_Abuses_Abuse $abuse
     *
     * @return bool|int|null
     */
    public function save(Model_Abuses_Abuse $abuse)
    {
        if ($abuse->abuseId) {
            $result = parent::_saveDifferencesByIndex($abuse->abuseId, $abuse, new \Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($abuse, new \Database_Main(), self::TABLE, self::INDEX);
            $abuse->abuseId = $result;
        }

        return $result;
    }

    public function delete(Model_Abuses_Abuse $abuse)
    {
        return parent::_deleteByIndex($abuse->abuseId, new \Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new  Model_Abuses_Abuse($this), new \Database_Main(), self::TABLE);

        return $query;
    }
}
