<?php

namespace Service\Grabber;

/**
 * Class Model_Grabbers_Grabber
 *
 * @package Service\Grabber
 *
 * @property int $groupId
 * @property int $userId
 * @property int $grabberId
 * @property string $ownerId
 * @property string $itemId
 * @property bool $isActive
 * @property string $title
 * @property string $url
 * @property int $dateCreate
 * @property int $interval
 * @property bool $linkDelete
 * @property string $hashtags
 * @property int $hashtagsPos
 * @property string $photo
 * @property int $dateValid
 * @property int $datePost
 * @property bool $timeLimit
 * @property int $timeHourFrom
 * @property int $timeMinuteFrom
 * @property int $timeHourTo
 * @property int $timeMinuteTo
 * @property int $maxLength
 * @property bool $photoInGroup
 * @property bool $adsLimit
 * @property int $adsInterval
 * @property int $isWatermark
 * @property string $watermark
 * @property string $watermarkText
 * @property float $watermarkTextOpacity
 * @property int $watermarkTextPos
 * @property string $watermarkFont
 * @property string $watermarkColor
 * @property float $watermarkOpacity
 * @property int $watermarkPos
 * @property int $watermarkMargin
 * @property int $watermarkSize
 * @property int $watermarkMaxSize
 * @property bool $isFree
 * @property bool $wasFree
 * @property int $isFreeCount
 * @property bool $random
 * @property bool $userActive
 */
class Model_Groups_Group extends \Lib_ORM_Object
{
    private static $_PropertiesTypes;
    /** @var Model_Groups */
    protected $_factory;

    public function __construct(Model_Groups $factory)
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
                'groupId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED | self::FLAG_AUTOINCREMENT,
                'userId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'grabberId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'ownerId' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'itemId' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'isActive' => self::TYPE_BOOL | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'url' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'title' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'dateCreate' => self::TYPE_TIMESTAMP,
                'interval' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'linkDelete' => self::TYPE_BOOL | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'hashtags' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'hashtagsPos' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'photo' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'dateValid' => self::TYPE_TIMESTAMP,
                'datePost' => self::TYPE_TIMESTAMP,

                'timeLimit' => self::TYPE_BOOL | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'timeHourFrom' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'timeMinuteFrom' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'timeHourTo' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'timeMinuteTo' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'maxLength' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'photoInGroup' => self::TYPE_BOOL | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'adsLimit' => self::TYPE_BOOL | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'adsInterval' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,

                'isWatermark' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'watermark' => self::TYPE_STRING,

                'watermarkText' => self::TYPE_STRING,
                'watermarkTextOpacity' => self::TYPE_FLOAT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'watermarkTextPos' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'watermarkFont' => self::TYPE_STRING,
                'watermarkColor' => self::TYPE_STRING,

                'watermarkOpacity' => self::TYPE_FLOAT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'watermarkPos' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'watermarkMargin' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'watermarkSize' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'watermarkMaxSize' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,

                'isFree' => self::TYPE_BOOL | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'wasFree' => self::TYPE_BOOL | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'isFreeCount' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'random' => self::TYPE_BOOL | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'userActive' => self::TYPE_BOOL | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
            ];
        }

        return self::$_PropertiesTypes;
    }
}
