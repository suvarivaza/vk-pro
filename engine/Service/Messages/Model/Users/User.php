<?php

namespace Service\Messages;

/**
 * Class Model_Users_User
 *
 * @package Service\Messages
 *
 * @property int $messageUserId
 * @property int $messageId
 * @property int $userId
 * @property int $dateCreate
 * @property bool $isDone
 * @property int $isDoneDate
 * @property int $type
 * @property string $icon
 * @property string $text
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
                'messageUserId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL | self::FLAG_AUTOINCREMENT,
                'messageId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'userId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'dateCreate' => self::TYPE_TIMESTAMP,
                'isDone' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'isDoneDate' => self::TYPE_TIMESTAMP,
                'type' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'icon' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'text' => self::TYPE_STRING | self::FLAG_NOT_NULL,
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
