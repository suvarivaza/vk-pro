<?php

namespace Service\Tasks;

/**
 * Class Model_Tasks_Task
 *
 * @package Service\Tasks
 *
 * @property int $specialUserId
 * @property int $specialId
 * @property int $userId
 * @property int $dateCreate
 */
class Model_Specials_Users_User extends \Lib_ORM_Object
{
    private static $_PropertiesTypes;
    public $passwordConfirm = '';
    /** @var Model_Users */
    protected $_factory;

    public function __construct(Model_Specials_Users $factory)
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
                'specialUserId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL | self::FLAG_AUTOINCREMENT,
                'specialId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'userId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'dateCreate' => self::TYPE_TIMESTAMP,
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
