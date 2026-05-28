<?php

namespace Service\Users;

/**
 * Class Model_Emails_Email
 *
 * @package Service\Users
 *
 * @property int $emailId
 * @property int $userId
 * @property string $userEmail
 * @property string $uuid
 * @property int $dateCreate
 * @property bool $isSent
 * @property int $isSentDate
 * @property string $title
 * @property string $text
 */
class Model_Emails_Email extends \Lib_ORM_Object
{
    private static $_PropertiesTypes;
    public $isNew = false;
    /** @var Model_Emails */
    protected $_factory;

    public function __construct(Model_Emails $factory)
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
                'emailId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL | self::FLAG_AUTOINCREMENT,
                'userId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'userEmail' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'uuid' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'dateCreate' => self::TYPE_TIMESTAMP,
                'isSent' => self::TYPE_BOOL | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'isSentDate' => self::TYPE_TIMESTAMP,
                'title' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'text' => self::TYPE_STRING | self::FLAG_NOT_NULL,
            ];
        }

        return self::$_PropertiesTypes;
    }
}
