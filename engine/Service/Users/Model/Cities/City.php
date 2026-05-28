<?php

namespace Service\Users;

/**
 * Class Model_Cities_City
 *
 * @package Service\Users
 *
 * @property int $cityId
 * @property string $title
 * @property int $count
 * @property bool $isVisible
 */
class Model_Cities_City extends \Lib_ORM_Object
{
    private static $_PropertiesTypes;
    public $isNew = false;
    /** @var Model_Cities */
    protected $_factory;

    public function __construct(Model_Cities $factory)
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
                'cityId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'title' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'count' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'isVisible' => self::TYPE_BOOL | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
            ];
        }

        return self::$_PropertiesTypes;
    }
}
