<?php

namespace Service\Faq;

use Database_Main;
use Lib_Exception_UnknownProperty_Backtraced;
use Lib_ORM;
use Lib_ORM_Query;

/**
 * @property Model_Factory $factory
 */
class Model_Rubrics extends Lib_ORM
{
    public const TABLE = 'faq_rubrics';

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
        $rubric = new Model_Rubrics_Rubric($this);
        $rubric->dateCreate = time();
        $rubric->icon = '/img/icons/32/icon-help.png';

        return $rubric;
    }

    /**
     * @param $rubricId
     * @param bool $for_save
     *
     * @return null| Model_Rubrics_Rubric
     */
    public function getById($rubricId, $for_save = false)
    {
        $rubric = new  Model_Rubrics_Rubric($this);

        if (!parent::_getOneByIndex($rubricId, $rubric, new Database_Main(), self::TABLE, self::INDEX, $for_save)) {
            return null;
        }

        return $rubric;
    }

    public function getAll($for_save = false)
    {
        $rubric = new  Model_Rubrics_Rubric($this);

        return parent::_getCollectionAllByIndex($rubric, new Database_Main(), self::TABLE, self::INDEX, $for_save);
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
     * @param Model_Rubrics_Rubric $rubric
     *
     * @return bool|int|null
     */
    public function save(Model_Rubrics_Rubric $rubric)
    {
        if ($rubric->rubricId) {
            $result = parent::_saveDifferencesByIndex($rubric->rubricId, $rubric, new Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($rubric, new Database_Main(), self::TABLE, self::INDEX);
            $rubric->rubricId = $result;
        }

        return $result;
    }

    public function delete(Model_Rubrics_Rubric $rubric)
    {
        return parent::_deleteByIndex($rubric->rubricId, new Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return Lib_ORM_Query
     */
    public function query()
    {
        $query = new Lib_ORM_Query(new  Model_Rubrics_Rubric($this), new Database_Main(), self::TABLE);

        return $query;
    }
}
