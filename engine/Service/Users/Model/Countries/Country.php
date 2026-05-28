<?php

namespace Service\Users;

/**
 * Class Model_Countries_Country
 *
 * @package Service\Users
 *
 * @property int $countryId
 * @property string $title
 * @property int $count
 * @property bool $isVisible
 */
class Model_Countries_Country extends \Lib_ORM_Object
{
    private static $_PropertiesTypes;
    public $isNew = false;
    /** @var Model_Countries */
    protected $_factory;

    public function __construct(Model_Countries $factory)
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
                'countryId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'title' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'count' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'isVisible' => self::TYPE_BOOL | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
            ];
        }

        return self::$_PropertiesTypes;
    }
}
