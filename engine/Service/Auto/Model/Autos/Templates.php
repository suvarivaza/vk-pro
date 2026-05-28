<?php

namespace Service\Auto;

use Database_Main;
use Lib_Exception_UnknownProperty_Backtraced;
use Lib_ORM;
use Lib_ORM_Query;

/**
 * @property Model_Factory $factory
 * @property Model_Autos_Templates_Posts $posts
 */
class Model_Autos_Templates extends Lib_ORM
{
    public const TABLE = 'auto_templates';

    public const INDEX = 'PRIMARY';
    public const INDEX_USERID = 'i_userId';
    public const INDEX_AUTOID = 'i_autoId';
    public const INDEX_GROUPID = 'i_groupId';
    public const INDEX_AUTOID_ISACTIVE = 'i_autoId_isActive';

    /** @var Model_Factory */
    protected $_factory;

    /** @var Model_Autos_Templates_Posts */
    protected $_posts = null;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $template = new Model_Autos_Templates_Template($this);
        $template->postId = 0;
        $template->specialId = 0;
        $template->isArchive = false;
        $template->isActive = true;

        return $template;
    }

    /**
     * @param $templateId
     * @param bool $for_save
     *
     * @return null| Model_Autos_Templates_Template
     */
    public function getById($templateId, $for_save = false)
    {
        $template = new  Model_Autos_Templates_Template($this);

        if (!parent::_getOneByIndex($templateId, $template, new Database_Main(), self::TABLE, self::INDEX,
            $for_save)) {
            return null;
        }

        return $template;
    }

    /**
     * @param $userId
     * @param bool $for_save
     *
     * @return Model_Autos_Templates_Template[]
     */
    public function getByUserId($userId, $for_save = false)
    {
        $template = new  Model_Autos_Templates_Template($this);

        return parent::_getCollectionByIndex($userId, $template, new Database_Main(), self::TABLE, self::INDEX_USERID,
            $for_save);
    }

    /**
     * @param $autoId
     * @param bool $for_save
     *
     * @return Model_Autos_Templates_Template[]
     */
    public function getByAutoId($autoId, $for_save = false)
    {
        $template = new  Model_Autos_Templates_Template($this);

        return parent::_getCollectionByIndex($autoId, $template, new Database_Main(), self::TABLE, self::INDEX_AUTOID,
            $for_save);
    }

    /**
     * @param $autoId
     * @param bool $for_save
     *
     * @return Model_Autos_Templates_Template[]
     */
    public function getByGroupId($autoId, $for_save = false)
    {
        $template = new  Model_Autos_Templates_Template($this);

        return parent::_getCollectionByIndex($autoId, $template, new Database_Main(), self::TABLE, self::INDEX_GROUPID,
            $for_save);
    }

    /**
     * @param $autoId
     * @param bool $for_save
     *
     * @return Model_Autos_Templates_Template[]
     */
    public function getByAutoIdIsActive($autoId, $isActive, $for_save = false)
    {
        $template = new  Model_Autos_Templates_Template($this);

        return parent::_getCollectionByIndex([$autoId . $isActive], $template, new Database_Main(), self::TABLE,
            self::INDEX_AUTOID_ISACTIVE, $for_save);
    }

    public function __get($name)
    {
        switch ($name) {
            case 'factory':
                return $this->_factory;
            case 'posts':
                if ($this->_posts === null) {
                    $this->_posts = new Model_Autos_Templates_Posts($this->factory);
                }

                return $this->_posts;
        }

        throw new Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }

    /**
     * @param Model_Autos_Templates_Template $template
     *
     * @return bool|int|null
     */
    public function save(Model_Autos_Templates_Template $template)
    {
        if ($template->templateId) {
            $result = parent::_saveDifferencesByIndex($template->templateId, $template, new Database_Main(),
                self::TABLE, self::INDEX);
        } else {
            $result = parent::_insert($template, new Database_Main(), self::TABLE, self::INDEX);
            $template->templateId = $result;
        }

        return $result;
    }

    public function delete(Model_Autos_Templates_Template $template)
    {
        return parent::_deleteByIndex($template->templateId, new Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return Lib_ORM_Query
     */
    public function query()
    {
        $query = new Lib_ORM_Query(new  Model_Autos_Templates_Template($this), new Database_Main(), self::TABLE);

        return $query;
    }
}
