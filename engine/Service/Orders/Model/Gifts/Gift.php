<?php

namespace Service\Orders;

/**
 * Class Model_Gifts_Gift
 *
 * @package Service\Orders
 *
 * @property int $giftId
 * @property int $adminId
 * @property int $userId
 * @property int $packId
 * @property int $dateCreate
 * @property int $dateValid
 * @property bool $isActive
 * @property int $isActiveDate
 */
class Model_Gifts_Gift extends \Lib_ORM_Object
{
    private static $_PropertiesTypes;
    public $passwordConfirm = '';
    /** @var Model_Orders */
    protected $_factory;

    public function __construct(Model_Gifts $factory)
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
                'giftId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED | self::FLAG_AUTOINCREMENT,
                'adminId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'userId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'packId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,

                'dateCreate' => self::TYPE_TIMESTAMP,
                'dateValid' => self::TYPE_TIMESTAMP,

                'isActive' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'isActiveDate' => self::TYPE_TIMESTAMP,
            ];
        }

        return self::$_PropertiesTypes;
    }
}
