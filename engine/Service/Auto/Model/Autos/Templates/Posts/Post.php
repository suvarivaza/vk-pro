<?php

namespace Service\Auto;

use Lib_ORM_Object;

/**
 * Class Model_Autos_Templates_Posts_Post
 *
 * @package Service\Auto
 *
 * @property int $postId
 * @property int $templateId
 * @property int $itemId
 */
class Model_Autos_Templates_Posts_Post extends Lib_ORM_Object
{
    private static $_PropertiesTypes;
    /** @var Model_Autos_Templates_Posts */
    protected $_factory;

    public function __construct(Model_Autos_Templates_Posts $factory)
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
                'postId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL | self::FLAG_AUTOINCREMENT,
                'templateId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'itemId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
            ];
        }

        return self::$_PropertiesTypes;
    }
}
