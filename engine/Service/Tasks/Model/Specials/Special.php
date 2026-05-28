<?php

namespace Service\Tasks;

/**
 * Class Model_Specials_Special
 *
 * @package Service\Tasks
 *
 * @property int $specialId
 * @property int $userId
 * @property int $dateCreate
 * @property int $dateValid
 * @property bool $isActive
 */
class Model_Specials_Special extends \Lib_ORM_Object
{
    private static $_PropertiesTypes;
    /** @var Model_Specials */
    protected $_factory;

    public function __construct(Model_Specials $factory)
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
                'specialId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED | self::FLAG_AUTOINCREMENT,
                'userId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'dateCreate' => self::TYPE_TIMESTAMP,
                'dateValid' => self::TYPE_TIMESTAMP,
                'isActive' => self::TYPE_BOOL | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'slots' => self::TYPE_STRING | self::FLAG_NOT_NULL,
            ];
        }

        return self::$_PropertiesTypes;
    }

    public function getSlots()
    {
        $slots = json_decode($this->slots, true);

        if (!is_array($slots)) {
            $slots = [];
        }

        return $slots;
    }

    public function setSlots($slots = [])
    {
        $this->slots = json_encode($slots, JSON_UNESCAPED_UNICODE);
    }
}
