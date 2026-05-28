<?php

namespace Service\Tasks;

/**
 * Class Model_Abuses_Abuse
 *
 * @package Service\Tasks
 *
 * @property int $abuseId
 * @property int $taskId
 * @property int $userId
 * @property int $reason
 * @property bool $isDone
 * @property string $comment
 * @property int $dateCreate
 */
class Model_Abuses_Abuse extends \Lib_ORM_Object
{
    private static $_PropertiesTypes;
    /** @var Model_Users */
    protected $_factory;

    public function __construct(Model_Abuses $factory)
    {
        parent::__construct();
        $this->_factory = $factory;
    }

    /**
     * @return array
     */
    public static function GetPropertiesTypes()
    {
        if (null === self::$_PropertiesTypes) {
            self::$_PropertiesTypes = [
                'abuseId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL | self::FLAG_AUTOINCREMENT,
                'taskId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'userId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'reason' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'isDone' => self::TYPE_BOOL | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'comment' => self::TYPE_STRING,
                'dateCreate' => self::TYPE_TIMESTAMP,
            ];
        }

        return self::$_PropertiesTypes;
    }

    public function getTask()
    {
        $task = $this->_factory->factory->tasks->getById($this->taskId);

        return $task;
    }

    public function getUser()
    {
        $factoryUsers = new \Service\Users\Model_Factory();
        $user = $factoryUsers->users->getById($this->userId);

        return $user;
    }
}
