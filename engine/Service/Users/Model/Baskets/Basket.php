<?php

namespace Service\Users;

/**
 * Class Model_Users_User
 *
 * @package Service\Users
 *
 * @property int $basketId
 * @property int $userId
 * @property string $userCookie
 * @property int $count
 * @property int $sum
 * @property string $parts
 */
class Model_Baskets_Basket extends \Lib_ORM_Object
{
    private static $_PropertiesTypes;
    public $passwordConfirm = '';
    /** @var Model_Users */
    protected $_factory;

    public function __construct(Model_Baskets $factory)
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
                'basketId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL | self::FLAG_AUTOINCREMENT,
                'userId' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'userCookie' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'count' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'sum' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'parts' => self::TYPE_STRING,
            ];
        }

        return self::$_PropertiesTypes;
    }

    public function setParts($parts)
    {
        $this->parts = json_encode($parts, JSON_UNESCAPED_UNICODE);
    }

    public function getParts()
    {
        $parts = json_decode($this->parts, true);

        if (!is_array($parts)) {
            $parts = [];
        }

        return $parts;
    }
}
