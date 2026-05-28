<?php

namespace Service\Messages;

/**
 * Class Model_Messages_Message
 *
 * @package Service\Messages
 *
 * @property int $messageId
 * @property int $userId
 * @property int $type
 * @property int $dateCreate
 * @property bool $isDone
 * @property string $text
 */
class Model_Messages_Message extends \Lib_ORM_Object
{
    private static $_PropertiesTypes;
    public $passwordConfirm = '';
    /** @var Model_Messages */
    protected $_factory;

    public function __construct(Model_Messages $factory)
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
                'messageId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL | self::FLAG_AUTOINCREMENT,
                'userId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'type' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'dateCreate' => self::TYPE_TIMESTAMP,
                'isDone' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'text' => self::TYPE_STRING | self::FLAG_NOT_NULL,
            ];
        }

        return self::$_PropertiesTypes;
    }
}
