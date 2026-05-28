<?php

namespace Service\Faq;

use Lib_ORM_Object;

/**
 * Class Model_Faq_Faq
 *
 * @package Service\Faq
 *
 * @property int $faqId
 * @property int $rubricId
 * @property int $userId
 * @property int $adminId
 * @property int $dateCreate
 * @property bool $isAnswer
 * @property int $isAnswerDate
 * @property string $question
 * @property string $answer
 */
class Model_Faq_Faq extends Lib_ORM_Object
{
    private static $_PropertiesTypes;
    public $passwordConfirm = '';
    /** @var Model_Faq */
    protected $_factory;

    public function __construct(Model_Faq $factory)
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
                'faqId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL | self::FLAG_AUTOINCREMENT,
                'rubricId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'userId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'adminId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'dateCreate' => self::TYPE_TIMESTAMP,
                'isAnswer' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'isAnswerDate' => self::TYPE_TIMESTAMP,
                'question' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'answer' => self::TYPE_STRING | self::FLAG_NOT_NULL,
            ];
        }

        return self::$_PropertiesTypes;
    }
}
