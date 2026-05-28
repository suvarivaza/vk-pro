<?php

namespace Service\Users;

/**
 * @property Model_Factory $factory
 */
class Model_Emails extends \Lib_ORM
{
    public const TABLE = 'emails';

    public const INDEX_PRIMARY = 'PRIMARY';

    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $email = new Model_Emails_Email($this);
        $email->isNew = true;

        return $email;
    }

    /**
     * @param $emailId
     * @param bool $for_save
     *
     * @return null|  Model_Emails_Email
     */
    public function getById($emailId, $for_save = false)
    {
        $email = new   Model_Emails_Email($this);

        if (!parent::_getOneByIndex($emailId, $email, new \Database_Main(), self::TABLE, self::INDEX_PRIMARY,
            $for_save)) {
            return null;
        }

        return $email;
    }

    public function __get($name)
    {
        switch ($name) {
            case 'factory':
                return $this->_factory;
        }

        throw new \Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }

    /**
     * @param Model_Emails_Email $email
     *
     * @return bool|int|null
     */
    public function save(Model_Emails_Email $email)
    {
        if ($email->emailId) {
            $result = parent::_saveDifferencesByIndex($email->emailId, $email, new \Database_Main(), self::TABLE,
                self::INDEX_PRIMARY);
        } else {
            $result = parent::_insert($email, new \Database_Main(), self::TABLE, self::INDEX_PRIMARY);
            $email->emailId = $result;
        }

        return $result;
    }

    public function delete(Model_Emails_Email $email)
    {
        return parent::_deleteByIndex($email->emailId, new \Database_Main(), self::TABLE, self::INDEX_PRIMARY);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new   Model_Emails_Email($this), new \Database_Main(), self::TABLE);

        return $query;
    }
}
