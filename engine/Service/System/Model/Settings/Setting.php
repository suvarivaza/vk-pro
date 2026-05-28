<?php

namespace Service\System;

/**
 * Class Model_Users_User
 *
 * @package Service\Users
 *
 * @property int $settingId
 * @property string $name
 * @property string $value
 */
class Model_Settings_Setting extends \Lib_ORM_Object
{
    public const TABLE = 'settings';

    public const INDEX = 'PRIMARY';

    public const INDEX_NAME = 'u_name';
    private static $_PropertiesTypes;
    /** @var Model_Settings */
    protected $_factory;

    public function __construct(Model_Settings $factory)
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
                'settingId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL | self::FLAG_AUTOINCREMENT,
                'name' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'value' => self::TYPE_STRING | self::FLAG_NOT_NULL,
            ];
        }

        return self::$_PropertiesTypes;
    }
}
