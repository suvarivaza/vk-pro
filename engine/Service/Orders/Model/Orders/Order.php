<?php

namespace Service\Orders;

/**
 * Class Model_Orders_Order
 *
 * @package Service\Faq
 *
 * @property int $orderId
 * @property int $userId
 * @property int $dateCreate
 * @property bool $isOrdered
 * @property int $isOrderedDate
 * @property string $type
 * @property int $packId
 * @property int $balance
 * @property float $price
 * @property string $invoiceId
 * @property string $token
 * @property bool $isAuto
 * @property bool $isPosting
 * @property bool $isGrabber
 * @property bool $isSpecial
 * @property bool $isBot
 * @property int $isAutoMonth
 * @property int $isPostingMonth
 * @property int $isGrabberMonth
 * @property int $isSpecialMonth
 * @property int $isBotMonth
 * @property bool $isBuy
 * @property int $isBuyDate
 * @property bool $isReferrer
 * @property int $monthId
 * @property int $giftId
 * @property bool $isSlot
 */
class Model_Orders_Order extends \Lib_ORM_Object
{
    private static $_PropertiesTypes;
    public $passwordConfirm = '';
    /** @var Model_Orders */
    protected $_factory;
    private $_user = null;

    public function __construct(Model_Orders $factory)
    {
        parent::__construct();
        $this->_factory = $factory;
    }

    /**
     * Строкая типизация
     * Если тип передаваемых данных не будет соответвствовать возникнен фатальная ошибка!
     * @return array
     */
    public static function GetPropertiesTypes()
    {
        if (null === self::$_PropertiesTypes) {
            self::$_PropertiesTypes = [
                'orderId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED | self::FLAG_AUTOINCREMENT,
                'userId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'dateCreate' => self::TYPE_TIMESTAMP,
                'isOrdered' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'isOrderedDate' => self::TYPE_TIMESTAMP,
                'type' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'packId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'balance' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'price' => self::TYPE_FLOAT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'invoiceId' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'token' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'isAuto' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'isPosting' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'isGrabber' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'isSpecial' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'isBot' => self::TYPE_BOOL | self::FLAG_NOT_NULL,

                'isAutoMonth' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'isPostingMonth' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'isGrabberMonth' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'isSpecialMonth' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'isBotMonth' => self::TYPE_INT | self::FLAG_NOT_NULL,

                'isBuy' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'isBuyDate' => self::TYPE_TIMESTAMP,

                'isReferrer' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'monthId' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'giftId' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'isSlot' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
            ];
        }

        return self::$_PropertiesTypes;
    }

    public function getUser()
    {
        if ($this->_user === null) {
            $factory = new \Service\Users\Model_Factory();
            $this->_user = $factory->users->getById($this->userId);
        }

        return $this->_user;
    }
}
