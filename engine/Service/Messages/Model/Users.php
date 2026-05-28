<?php

namespace Service\Messages;

/**
 * @property Model_Factory $factory
 */
class Model_Users extends \Lib_ORM
{
    public const TABLE = 'messages_users';

    public const INDEX = 'PRIMARY';
    public const INDEX_USERID_ISDONE = 'i_userId_isDone';
    public const INDEX_MESSAGEID_USERID = 'i_messageId_userId';

    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $taskUser = new Model_Users_User($this);
        $taskUser->messageId = 0;
        $taskUser->type = Model_Config::TYPE_USER;
        $taskUser->icon = '';
        $taskUser->dateCreate = time();

        return $taskUser;
    }

    /**
     * @param $messageUserId
     * @param bool $for_save
     *
     * @return null| Model_Users_User
     */
    public function getById($messageUserId, $for_save = false)
    {
        $messageUser = new  Model_Users_User($this);

        if (!parent::_getOneByIndex($messageUserId, $messageUser, new \Database_Main(), self::TABLE, self::INDEX,
            $for_save)) {
            return null;
        }

        return $messageUser;
    }

    public function getByMessageIdUserId($messageId, $userId, $for_save = false)
    {
        $messageUser = new  Model_Users_User($this);

        if (!parent::_getOneByIndex([$messageId, $userId], $messageUser, new \Database_Main(), self::TABLE,
            self::INDEX_MESSAGEID_USERID, $for_save)) {
            return null;
        }

        return $messageUser;
    }

    public function getByUserId($userId, $isDone, $for_save = false)
    {
        $messageUser = new  Model_Users_User($this);

        return parent::_getCollectionByIndex([$userId, $isDone], $messageUser, new \Database_Main(), self::TABLE,
            self::INDEX_USERID_ISDONE, $for_save);
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
     * @param Model_Users_User $task
     *
     * @return bool|int|null
     */
    public function save(Model_Users_User $messageUser)
    {
        if ($messageUser->messageUserId) {
            $result = parent::_saveDifferencesByIndex($messageUser->messageUserId, $messageUser, new \Database_Main(),
                self::TABLE, self::INDEX);
        } else {
            $result = parent::_insert($messageUser, new \Database_Main(), self::TABLE, self::INDEX);
            $messageUser->messageUserId = $result;
        }

        return $result;
    }

    public function delete(Model_Users_User $messageUser)
    {
        return parent::_deleteByIndex($messageUser->messageUserId, new \Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new  Model_Users_User($this), new \Database_Main(), self::TABLE);

        return $query;
    }
}
