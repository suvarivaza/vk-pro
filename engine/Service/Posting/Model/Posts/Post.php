<?php

namespace Service\Posting;

/**
 * Class Model_Posts_Post
 *
 * @package Service\Postring
 *
 * @property int $postId
 * @property int $groupId
 * @property int $userId
 * @property int $dateCreate
 * @property int $datePost
 * @property bool $isPost
 * @property int $isPostDate
 * @property string $text
 * @property string $attachments
 * @property bool $signature
 * @property bool $ads
 */
class Model_Posts_Post extends \Lib_ORM_Object
{
    private static $_PropertiesTypes;
    /** @var Model_Posts */
    protected $_factory;

    public function __construct(Model_Posts $factory)
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
                'postId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL | self::FLAG_AUTOINCREMENT,
                'userId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'groupId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'dateCreate' => self::TYPE_TIMESTAMP,
                'datePost' => self::TYPE_TIMESTAMP,
                'isPost' => self::TYPE_BOOL | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'isPostDate' => self::TYPE_TIMESTAMP,
                'text' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'attachments' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'signature' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'ads' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
            ];
        }

        return self::$_PropertiesTypes;
    }

    public function setAttachments($attachments)
    {
        $this->attachments = json_encode($attachments);
    }

    public function getAttachments()
    {
        return json_decode($this->attachments, true);
    }
}
