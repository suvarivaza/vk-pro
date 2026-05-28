<?php

namespace Service\News;

use Lib_ORM_Object;

/**
 * Class Model_News_New
 *
 * @package Service\News
 *
 * @property int $newId
 * @property string $alias
 * @property string $title
 * @property string $keywords
 * @property string $desc
 * @property string $text
 * @property int $dateCreate
 * @property int $dateUpdate
 * @property int $userId
 * @property bool $announce
 * @property string $photo
 */
class Model_News_New extends Lib_ORM_Object
{
    private static $_PropertiesTypes;
    public $passwordConfirm = '';
    /** @var Model_News */
    protected $_factory;

    public function __construct(Model_News $factory)
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
                'newId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL | self::FLAG_AUTOINCREMENT,
                'alias' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'title' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'keywords' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'desc' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'text' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'dateCreate' => self::TYPE_TIMESTAMP,
                'dateUpdate' => self::TYPE_TIMESTAMP,
                'userId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'announce' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'photo' => self::TYPE_STRING | self::FLAG_NOT_NULL,
            ];
        }

        return self::$_PropertiesTypes;
    }

    public function setPhoto($photo)
    {
        if (!is_array($photo)) {
            $photo = [];
        }
        $this->photo = json_encode($photo, JSON_UNESCAPED_UNICODE);
    }

    public function getPhoto()
    {
        $photo = json_decode($this->photo, true);

        if (!is_array($photo)) {
            $photo = [];
        }

        return $photo;
    }
}
