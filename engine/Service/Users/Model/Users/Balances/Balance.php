<?php

namespace Service\Users;

/**
 * Class Model_Users_Balances_Balance
 *
 * @package Service\Users
 *
 * @property int $balanceId
 * @property int $userId
 * @property bool $isBonus
 * @property bool $isCompensation
 * @property bool $isPenalty
 * @property bool $isTask
 * @property bool $isBot
 * @property bool $isReferrer
 * @property float $balance
 * @property float $balanceFrom
 * @property float $balanceTo
 * @property string $comment
 * @property int $dateCreate
 */
class Model_Users_Balances_Balance extends \Lib_ORM_Object
{
    private static $_PropertiesTypes;
    /** @var Model_Users */
    protected $_factory;

    public function __construct(Model_Users_Balances $factory)
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
                'balanceId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED | self::FLAG_AUTOINCREMENT,
                'userId' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'isBonus' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'isCompensation' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'isPenalty' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'isTask' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'isBot' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'isReferrer' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'balance' => self::TYPE_FLOAT | self::FLAG_NOT_NULL,
                'balanceFrom' => self::TYPE_FLOAT | self::FLAG_NOT_NULL,
                'balanceTo' => self::TYPE_FLOAT | self::FLAG_NOT_NULL,
                'comment' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'dateCreate' => self::TYPE_TIMESTAMP,
            ];
        }

        return self::$_PropertiesTypes;
    }
}
