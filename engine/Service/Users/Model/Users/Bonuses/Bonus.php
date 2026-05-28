<?php

namespace Service\Users;

/**
 * Class Model_Users_Bonuses_Bonus
 *
 * @package Service\Users
 *
 * @property int $userBonusId
 * @property int $userId
 * @property int $day
 * @property int $week
 * @property int $year
 * @property int $type
 * @property int $balanceId
 */
class Model_Users_Bonuses_Bonus extends \Lib_ORM_Object
{
    private static $_PropertiesTypes;
    /** @var Model_Users */
    protected $_factory;

    public function __construct(Model_Users_Bonuses $factory)
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
                'userBonusId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED | self::FLAG_AUTOINCREMENT,
                'userId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'day' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'week' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'year' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'type' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'balanceId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
            ];
        }

        return self::$_PropertiesTypes;
    }
}
