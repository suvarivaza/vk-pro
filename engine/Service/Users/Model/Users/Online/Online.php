<?php

namespace Service\Users;

/**
 * Class Model_Users_Karma_Karma
 *
 * @package Service\Users
 *
 * @property int $userOnlineId
 * @property string $date
 * @property int $count
 */
class Model_Users_Online_Online extends \Lib_ORM_Object
{
    private static $_PropertiesTypes;
    /** @var Model_Users */
    protected $_factory;

    public function __construct(Model_Users_Online $factory)
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
                'userOnlineId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED | self::FLAG_AUTOINCREMENT,
                'date' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'count' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
            ];
        }

        return self::$_PropertiesTypes;
    }
}
