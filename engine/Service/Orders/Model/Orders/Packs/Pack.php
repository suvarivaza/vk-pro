<?php

namespace Service\Orders;

/**
 * Class Model_Faq_Faq
 *
 * @package Service\Faq
 *
 * @property int $packId
 * @property string $title
 * @property bool $isReferrer
 * @property int $balance
 * @property int $bonus
 * @property float $price
 * @property float $profit
 * @property int $serviceCount
 * @property bool $serviceAll
 * @property int $serviceMonth
 */
class Model_Orders_Packs_Pack extends \Lib_ORM_Object
{
    private static $_PropertiesTypes;
    public $passwordConfirm = '';
    /** @var Model_Orders_Packs */
    protected $_factory;

    public function __construct(Model_Orders_Packs $factory)
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
                'packId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL | self::FLAG_AUTOINCREMENT,
                'title' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'isReferrer' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'balance' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'bonus' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'price' => self::TYPE_FLOAT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'profit' => self::TYPE_FLOAT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'serviceCount' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'serviceAll' => self::TYPE_BOOL | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'serviceMonth' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
            ];
        }

        return self::$_PropertiesTypes;
    }
}
