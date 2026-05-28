<?php

namespace Service\Users;

/**
 * Class Model_Users_Referrers_Referrer
 *
 * @package Service\Users
 *
 * @property int $requestId
 * @property int $userId
 * @property float $balanceRef
 * @property float $balanceFee
 * @property float $balanceTotal
 * @property int $status
 * @property int $dateCreate
 */
class Model_Users_Requests_Request extends \Lib_ORM_Object
{
    private static $_PropertiesTypes;
    /** @var Model_Users */
    protected $_factory;

    public function __construct(Model_Users_Requests $factory)
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
                'requestId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED | self::FLAG_AUTOINCREMENT,
                'userId' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'balanceRef' => self::TYPE_FLOAT | self::FLAG_NOT_NULL,
                'balanceFee' => self::TYPE_FLOAT | self::FLAG_NOT_NULL,
                'balanceTotal' => self::TYPE_FLOAT | self::FLAG_NOT_NULL,
                'status' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'dateCreate' => self::TYPE_TIMESTAMP,
            ];
        }

        return self::$_PropertiesTypes;
    }
}
