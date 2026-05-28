<?php

namespace Service\Auto;

use Database_Main;
use Lib_Exception_UnknownProperty_Backtraced;
use Lib_ORM;
use Lib_ORM_Query;

/**
 * @property Model_Factory $factory
 */
class Model_Autos_Templates_Posts extends Lib_ORM
{
    public const TABLE = 'auto_templates_posts';

    public const INDEX = 'PRIMARY';
    public const INDEX_TEMPLATEID_POSTID = 'i_templateId_itemId';

    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $post = new Model_Autos_Templates_Posts_Post($this);

        return $post;
    }

    /**
     * @param $postId
     * @param bool $for_save
     *
     * @return null| Model_Autos_Templates_Posts_Post
     */
    public function getById($postId, $for_save = false)
    {
        $post = new  Model_Autos_Templates_Posts_Post($this);

        if (!parent::_getOneByIndex($postId, $post, new Database_Main(), self::TABLE, self::INDEX, $for_save)) {
            return null;
        }

        return $post;
    }

    /**
     * @param $templateId
     * @param $itemId
     * @param bool $for_save
     *
     * @return Model_Autos_Templates_Posts_Post
     */
    public function getByTemplateIdItemId($templateId, $itemId, $for_save = false)
    {
        $post = new  Model_Autos_Templates_Posts_Post($this);

        if (!parent::_getOneByIndex([$templateId, $itemId], $post, new Database_Main(), self::TABLE,
            self::INDEX_TEMPLATEID_POSTID, $for_save)) {
            return null;
        }

        return $post;
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
     * @param Model_Autos_Templates_Posts_Post $post
     *
     * @return bool|int|null
     */
    public function save(Model_Autos_Templates_Posts_Post $post)
    {
        if ($post->postId) {
            $result = parent::_saveDifferencesByIndex($post->postId, $post, new Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($post, new Database_Main(), self::TABLE, self::INDEX);
            $post->postId = $result;
        }

        return $result;
    }

    public function delete(Model_Autos_Templates_Posts_Post $post)
    {
        return parent::_deleteByIndex($post->postId, new Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return Lib_ORM_Query
     */
    public function query()
    {
        $query = new Lib_ORM_Query(new  Model_Autos_Templates_Posts_Post($this), new Database_Main(), self::TABLE);

        return $query;
    }
}
