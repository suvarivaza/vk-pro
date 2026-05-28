<?php

namespace Service\Faq;

use Lib_ORM_Object;

/**
 * Class Model_Faq_Faq
 *
 * @package Service\Faq
 *
 * @property int $rubricId
 * @property int $dateCreate
 * @property string $title
 * @property string $icon
 */
class Model_Rubrics_Rubric extends Lib_ORM_Object
{
    private static $_PropertiesTypes;
    public $passwordConfirm = '';
    /** @var Model_Rubrics */
    protected $_factory;

    public function __construct(Model_Rubrics $factory)
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
                'rubricId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL | self::FLAG_AUTOINCREMENT,
                'dateCreate' => self::TYPE_TIMESTAMP,
                'title' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'icon' => self::TYPE_STRING | self::FLAG_NOT_NULL,
            ];
        }

        return self::$_PropertiesTypes;
    }

    /**
     * @return Model_Questions_Question[]
     */
    public function getQuestions()
    {
        $query = $this->_factory->factory->questions->query()->limit(4)->sort('qId', 'DESC');
        $query->filter->fieldValue('rubricId', '=', $this->rubricId)
            ->fieldValue('isVisible', '&', 2 & 4);

        $it = $query->iterator();
        $list = [];

        foreach ($it as $question) {
            $list[] = $question;
        }

        return $list;
    }
}
