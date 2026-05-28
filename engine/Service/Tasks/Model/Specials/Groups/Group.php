<?php

namespace Service\Tasks;

/**
 * Class Model_Faq_Faq
 *
 * @package Service\Tasks
 *
 * @property int $groupId
 * @property int $specialId
 * @property int $userId
 * @property int $dateCreate
 * @property string $title
 * @property string $url
 * @property string $ownerId
 * @property string $photo
 * @property int $dateValid
 * @property bool $isFree
 * @property bool $wasFree
 * @property int $isFreeCount
 * @property bool $isActive
 * @property int $lastUpdate
 */
class Model_Specials_Groups_Group extends \Lib_ORM_Object
{
    private static $_PropertiesTypes;
    public $passwordConfirm = '';
    /** @var Model_Specials_Groups */
    protected $_factory;

    public function __construct(Model_Specials_Groups $factory)
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
                'groupId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL | self::FLAG_AUTOINCREMENT,
                'specialId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'userId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'dateCreate' => self::TYPE_TIMESTAMP,
                'ownerId' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'title' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'url' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'photo' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'dateValid' => self::TYPE_TIMESTAMP,
                'isFree' => self::TYPE_BOOL | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'wasFree' => self::TYPE_BOOL | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'isFreeCount' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'isActive' => self::TYPE_BOOL | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'lastUpdate' => self::TYPE_TIMESTAMP,
            ];
        }

        return self::$_PropertiesTypes;
    }
}
