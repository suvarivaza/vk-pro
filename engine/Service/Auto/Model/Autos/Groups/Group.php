<?php

namespace Service\Auto;

use Lib_ORM_Object;

/**
 * Class Model_Faq_Faq
 *
 * @package Service\Faq
 *
 * @property int $autoGroupId
 * @property int $userId
 * @property int $autoId
 * @property int $ownerId
 * @property string $title
 * @property string $url
 * @property string $photo
 * @property int $dateValid
 * @property string $code
 * @property bool $isFree
 * @property bool $wasFree
 * @property int $isFreeCount
 */
class Model_Autos_Groups_Group extends Lib_ORM_Object
{
    private static $_PropertiesTypes;
    public $passwordConfirm = '';
    /** @var Model_Autos_Groups */
    protected $_factory;

    public function __construct(Model_Autos_Groups $factory)
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
                'autoGroupId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL | self::FLAG_AUTOINCREMENT,
                'userId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'autoId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'ownerId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'title' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'url' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'photo' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'dateValid' => self::TYPE_TIMESTAMP,
                'code' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'isFree' => self::TYPE_BOOL | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'wasFree' => self::TYPE_BOOL | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'isFreeCount' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
            ];
        }

        return self::$_PropertiesTypes;
    }
}
