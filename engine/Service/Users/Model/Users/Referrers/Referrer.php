<?php

namespace Service\Users;

/**
 * Class Model_Users_Referrers_Referrer
 *
 * @package Service\Users
 *
 * @property int $referrerId
 * @property int $userId
 * @property float $balanceRef
 * @property float $balanceRefFrom
 * @property float $balanceRefTo
 * @property int $from
 * @property int $dateCreate
 * @property string $comment
 */
class Model_Users_Referrers_Referrer extends \Lib_ORM_Object
{
    private static $_PropertiesTypes;
    /** @var Model_Users */
    protected $_factory;

    public function __construct(Model_Users_Referrers $factory)
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
                'referrerId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED | self::FLAG_AUTOINCREMENT,
                'userId' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'balanceRef' => self::TYPE_FLOAT | self::FLAG_NOT_NULL,
                'balanceRefFrom' => self::TYPE_FLOAT | self::FLAG_NOT_NULL,
                'balanceRefTo' => self::TYPE_FLOAT | self::FLAG_NOT_NULL,
                'from' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'dateCreate' => self::TYPE_TIMESTAMP,
                'comment' => self::TYPE_STRING | self::FLAG_NOT_NULL,
            ];
        }

        return self::$_PropertiesTypes;
    }
}
