<?php

namespace Service\Faq;

use Database_Main;
use Lib_Exception_UnknownProperty_Backtraced;
use Lib_ORM;
use Lib_ORM_Query;

/**
 * @property Model_Factory $factory
 */
class Model_Questions extends Lib_ORM
{
    public const TABLE = 'faq_questions';

    public const INDEX = 'PRIMARY';

    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $question = new Model_Questions_Question($this);
        $question->dateCreate = time();
        $question->isVisible = 0;

        return $question;
    }

    /**
     * @param $questionId
     * @param bool $for_save
     *
     * @return null| Model_Questions_Question
     */
    public function getById($questionId, $for_save = false)
    {
        $question = new  Model_Questions_Question($this);

        if (!parent::_getOneByIndex($questionId, $question, new Database_Main(), self::TABLE, self::INDEX,
            $for_save)) {
            return null;
        }

        return $question;
    }

    public function __get($name)
    {
        switch ($name) {
            case 'factory':
                return $this->_factory;
        }

        throw new Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }

    /**
     * @param Model_Questions_Question $question
     *
     * @return bool|int|null
     */
    public function save(Model_Questions_Question $question)
    {
        if ($question->qId) {
            $result = parent::_saveDifferencesByIndex($question->qId, $question, new Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($question, new Database_Main(), self::TABLE, self::INDEX);
            $question->qId = $result;
        }

        return $result;
    }

    public function delete(Model_Questions_Question $question)
    {
        return parent::_deleteByIndex($question->qId, new Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return Lib_ORM_Query
     */
    public function query()
    {
        $query = new Lib_ORM_Query(new  Model_Questions_Question($this), new Database_Main(), self::TABLE);

        return $query;
    }

    public function getCount($userId)
    {
        $sql = 'SELECT COUNT(`qId`) FROM `' . self::TABLE . '` WHERE `userId` = ' . $userId . ' AND `isNewAnswer` = 1';
        $res = $this->factory->db->query($sql);
        $row = $res->fetch_row();

        return $row[0];
    }
}
