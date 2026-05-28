<?php

namespace Service\Users;

/**
 * Class Model_Cities_City
 *
 * @package Service\Users
 *
 * @property int $notificationId
 * @property int $userId
 * @property int $objectId
 * @property int $type
 * @property string $service
 * @property string $title
 * @property int $status
 */
class Model_Notifications_Notification extends \Lib_ORM_Object
{
    private static $_PropertiesTypes;
    public $isNew = false;
    /** @var Model_Cities */
    protected $_factory;

    public function __construct(Model_Notifications $factory)
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
                'notificationId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL | self::FLAG_AUTOINCREMENT,
                'userId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'objectId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'type' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'service' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'title' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'status' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
            ];
        }

        return self::$_PropertiesTypes;
    }
}
