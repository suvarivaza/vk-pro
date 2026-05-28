<?php

namespace Service\Bot;

use Lib_ORM_Object;

/**
 * Class Model_Bots_Bot
 *
 * @package Service\Bots
 *
 * @property int $botId
 * @property int $userId
 * @property int $dateCreate
 * @property int $dateValid
 * @property bool $isActive
 * @property int $isBot
 * @property bool $isPro
 * @property int $lastTask
 * @property int $lastLike
 * @property int $lastRepost
 * @property int $lastJoin
 * @property int $lastFriends
 * @property int $lastPoll
 * @property int $lastComment
 */
class Model_Bots_Bot extends Lib_ORM_Object
{
    private static $_PropertiesTypes;
    /** @var Model_Bots */
    protected $_factory;

    public function __construct(Model_Bots $factory)
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
                'botId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED | self::FLAG_AUTOINCREMENT,
                'userId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'dateCreate' => self::TYPE_TIMESTAMP,
                'dateValid' => self::TYPE_TIMESTAMP,
                'isActive' => self::TYPE_BOOL | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'isBot' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'isPro' => self::TYPE_BOOL | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'lastTask' => self::TYPE_TIMESTAMP,
                'lastLike' => self::TYPE_TIMESTAMP,
                'lastRepost' => self::TYPE_TIMESTAMP,
                'lastJoin' => self::TYPE_TIMESTAMP,
                'lastFriends' => self::TYPE_TIMESTAMP,
                'lastPoll' => self::TYPE_TIMESTAMP,
                'lastComment' => self::TYPE_TIMESTAMP,
            ];
        }

        return self::$_PropertiesTypes;
    }
}
