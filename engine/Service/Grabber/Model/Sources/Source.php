<?php

namespace Service\Grabber;

/**
 * Class Model_Grabbers_Grabber
 *
 * @package Service\Grabber
 *
 * @property int $sourceId
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
 * @property string $photo
 * @property int $dateValid
 * @property string $blacklist
 * @property bool $delText
 * @property bool $delHashtags
 * @property bool $delLinks
 * @property bool $delVKLinks
 * @property bool $delEmoji
 * @property bool $delVideo
 * @property bool $delPoll
 * @property bool $notPhoto
 * @property bool $notVideo
 * @property bool $notMusic
 * @property bool $notDoc
 * @property bool $notGif
 * @property bool $withPhoto
 * @property bool $withVideo
 * @property bool $withDoc
 * @property bool $withText
 * @property bool $withGif
 * @property string $filter
 * @property bool $notAdv
 * @property bool $notFixed
 * @property bool $notLink
 * @property bool $notVKLink
 * @property bool $notTextOnly
 * @property bool $notPhotoOnly
 * @property bool $notFromGroup
 * @property bool $withFromGroup
 * @property bool $addCopyright
 * @property string $copyrightTitle
 * @property int $copyrightType
 * @property int $copyrightPosition
 * @property int $maxLength
 * @property int $count
 * @property int $countAll
 */
class Model_Sources_Source extends \Lib_ORM_Object
{
    private static $_PropertiesTypes;
    /** @var Model_Groups */
    protected $_factory;

    public function __construct(Model_Sources $factory)
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
                'sourceId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED | self::FLAG_AUTOINCREMENT,
                'groupId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
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
                'photo' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'dateValid' => self::TYPE_TIMESTAMP,
                'blacklist' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'delText' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'delHashtags' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'delLinks' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'delVKLinks' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'delEmoji' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'delVideo' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'delPoll' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'notPhoto' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'notVideo' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'notMusic' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'notDoc' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'notGif' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'withPhoto' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'withVideo' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'withDoc' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'withText' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'withGif' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'filter' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'notAdv' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'notFixed' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'notLink' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'notVKLink' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'notTextOnly' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'notPhotoOnly' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'notFromGroup' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'withFromGroup' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'addCopyright' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'copyrightTitle' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'copyrightType' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'copyrightPosition' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'maxLength' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'count' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'countAll' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
            ];
        }

        return self::$_PropertiesTypes;
    }
}
