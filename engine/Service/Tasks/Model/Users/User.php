<?php

namespace Service\Tasks;

/**
 * Class Model_Tasks_Task
 *
 * @package Service\Tasks
 *
 * @property int $taskUserId
 * @property int $taskId
 * @property int $userId
 * @property int $uid
 * @property string $type
 * @property bool $isDone
 * @property int $isDoneDate
 * @property bool $isDel
 * @property bool $isBot
 * @property int $isDelDate
 * @property bool $isActive
 * @property int $views
 * @property int $countViews
 * @property int $votes
 */
class Model_Users_User extends \Lib_ORM_Object
{
    private static $_PropertiesTypes;
    public $passwordConfirm = '';
    /** @var Model_Users */
    protected $_factory;

    public function __construct(Model_Users $factory)
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
                'taskUserId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL | self::FLAG_AUTOINCREMENT,
                'taskId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'userId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'uid' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'type' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'isDone' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'isDoneDate' => self::TYPE_TIMESTAMP,
                'isBot' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'isDel' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'isDelDate' => self::TYPE_TIMESTAMP,
                'isActive' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'views' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'countViews' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'votes' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
            ];
        }

        return self::$_PropertiesTypes;
    }

    public function setPhoto($photo)
    {
        if (!is_array($photo)) {
            $photo = [];
        }
        $this->photo = json_encode($photo, JSON_UNESCAPED_UNICODE);
    }

    public function getPhoto()
    {
        $photo = json_decode($this->photo, true);

        if (!is_array($photo)) {
            $photo = [];
        }

        return $photo;
    }
}
