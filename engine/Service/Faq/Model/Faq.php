<?php

namespace Service\Faq;

use Database_Main;
use Lib_Exception_UnknownProperty_Backtraced;
use Lib_ORM;
use Lib_ORM_Query;

/**
 * @property Model_Factory $factory
 */
class Model_Faq extends Lib_ORM
{
    public const TABLE = 'faq';

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
        $faq = new Model_Faq_Faq($this);
        $faq->dateCreate = time();

        return $faq;
    }

    /**
     * @param $faqId
     * @param bool $for_save
     *
     * @return null| Model_Faq_Faq
     */
    public function getById($faqId, $for_save = false)
    {
        $faq = new  Model_Faq_Faq($this);

        if (!parent::_getOneByIndex($faqId, $faq, new Database_Main(), self::TABLE, self::INDEX, $for_save)) {
            return null;
        }

        return $faq;
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
     * @param Model_Faq_Faq $faq
     *
     * @return bool|int|null
     */
    public function save(Model_Faq_Faq $faq)
    {
        if ($faq->faqId) {
            $result = parent::_saveDifferencesByIndex($faq->faqId, $faq, new Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($faq, new Database_Main(), self::TABLE, self::INDEX);
            $faq->faqId = $result;
        }

        return $result;
    }

    public function delete(Model_Faq_Faq $faq)
    {
        return parent::_deleteByIndex($faq->faqId, new Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return Lib_ORM_Query
     */
    public function query()
    {
        $query = new Lib_ORM_Query(new  Model_Faq_Faq($this), new Database_Main(), self::TABLE);

        return $query;
    }
}
