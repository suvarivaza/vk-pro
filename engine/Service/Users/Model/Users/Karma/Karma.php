<?php

namespace Service\Users;

/**
 * Class Model_Users_Karma_Karma
 *
 * @package Service\Users
 *
 * @property int $karmaId
 * @property int $userId
 * @property int $taskId
 * @property bool $isBot
 * @property float $karma
 * @property float $karmaFrom
 * @property float $karmaTo
 * @property string $comment
 * @property int $dateCreate
 */
class Model_Users_Karma_Karma extends \Lib_ORM_Object
{
    private static $_PropertiesTypes;
    /** @var Model_Users */
    protected $_factory;

    public function __construct(Model_Users_Karma $factory)
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
                'karmaId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED | self::FLAG_AUTOINCREMENT,
                'userId' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'taskId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'isBot' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'karma' => self::TYPE_FLOAT | self::FLAG_NOT_NULL,
                'karmaFrom' => self::TYPE_FLOAT | self::FLAG_NOT_NULL,
                'karmaTo' => self::TYPE_FLOAT | self::FLAG_NOT_NULL,
                'comment' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'dateCreate' => self::TYPE_TIMESTAMP,
            ];
        }

        return self::$_PropertiesTypes;
    }
}
