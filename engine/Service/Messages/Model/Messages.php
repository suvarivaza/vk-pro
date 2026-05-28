<?php

namespace Service\Messages;

/**
 * @property Model_Factory $factory
 */
class Model_Messages extends \Lib_ORM
{
    public const TABLE = 'messages';

    public const INDEX = 'PRIMARY';
    public const INDEX_USERID = 'userId';
    public const INDEX_USERID_ISDONE = 'i_userId_isDone';

    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $message = new Model_Messages_Message($this);
        $message->userId = 0;
        $message->type = 0;
        $message->dateCreate = time();
        $message->text = '';
        $message->isDone = false;

        return $message;
    }

    /**
     * @param $messageId
     * @param bool $for_save
     *
     * @return null| Model_Messages_Message
     */
    public function getById($messageId, $for_save = false)
    {
        $message = new  Model_Messages_Message($this);

        if (!parent::_getOneByIndex($messageId, $message, new \Database_Main(), self::TABLE, self::INDEX, $for_save)) {
            return null;
        }

        return $message;
    }

    /**
     * @param $userId
     * @param $isDone
     * @param bool $for_save
     *
     * @return Model_Messages_Message[]
     */
    public function getByUserIdIsDone($userId, $isDone, $for_save = false)
    {
        $message = new  Model_Messages_Message($this);

        return parent::_getCollectionByIndex([$userId, $isDone], $message, new \Database_Main(), self::TABLE,
            self::INDEX_USERID_ISDONE, $for_save);
    }

    /**
     * @param $userId
     * @param bool $for_save
     *
     * @return Model_Messages_Message[]
     */
    public function getByUserId($userId, $for_save = false)
    {
        $message = new  Model_Messages_Message($this);

        return parent::_getCollectionByIndex($userId, $message, new \Database_Main(), self::TABLE, self::INDEX_USERID,
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
     * @param Model_Messages_Message $message
     *
     * @return bool|int|null
     */
    public function save(Model_Messages_Message $message)
    {
        if ($message->messageId) {
            $result = parent::_saveDifferencesByIndex($message->messageId, $message, new \Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($message, new \Database_Main(), self::TABLE, self::INDEX);
            $message->messageId = $result;
        }

        return $result;
    }

    public function delete(Model_Messages_Message $message)
    {
        return parent::_deleteByIndex($message->messageId, new \Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new  Model_Messages_Message($this), new \Database_Main(), self::TABLE);

        return $query;
    }
}
