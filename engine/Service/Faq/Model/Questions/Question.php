<?php

namespace Service\Faq;

use Lib_ORM_Object;

/**
 * Class Model_Faq_Faq
 *
 * @package Service\Faq
 *
 * @property int $qId
 * @property int $rubricId
 * @property int $userId
 * @property int $adminId
 * @property int $dateCreate
 * @property string $question
 * @property string $answer
 * @property int $isVisible
 * @property string $chat
 * @property bool $isNew
 * @property bool $isNewQuestion
 * @property bool $isNewAnswer
 */
class Model_Questions_Question extends Lib_ORM_Object
{
    private static $_PropertiesTypes;
    public $passwordConfirm = '';
    /** @var Model_Questions */
    protected $_factory;
    private $_user = false;

    public function __construct(Model_Questions $factory)
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
                'qId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL | self::FLAG_AUTOINCREMENT,
                'rubricId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'userId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'adminId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'dateCreate' => self::TYPE_TIMESTAMP,
                'question' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'answer' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'chat' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'isVisible' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'isNew' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'isNewQuestion' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'isNewAnswer' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
            ];
        }

        return self::$_PropertiesTypes;
    }

    public function getUser()
    {
        if (!$this->userId) {
            $this->_user = null;
        } elseif ($this->_user === false) {
            $factory = new \Service\Users\Model_Factory();
            $this->_user = $factory->users->getById($this->userId);
        }

        return $this->_user;
    }

    public function getChat()
    {
        return json_decode($this->chat, true);
    }

    public function setChat($chat)
    {
        $this->chat = json_encode($chat);
    }
}
