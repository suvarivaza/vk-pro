<?php

namespace Service\News;

use Database_Main;
use Lib_Exception_UnknownProperty_Backtraced;
use Lib_ORM;
use Lib_ORM_Query;

/**
 * @property Model_Factory $factory
 */
class Model_News extends Lib_ORM
{
    public const TABLE = 'news';

    public const INDEX = 'PRIMARY';
    public const INDEX_ALIAS = 'u_alias';

    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $new = new Model_News_New($this);
        $new->alias = '';
        $new->title = '';
        $new->keywords = '';
        $new->desc = '';
        $new->text = '';
        $new->dateUpdate = time();
        $new->dateCreate = time();
        $new->announce = false;
        $new->setPhoto([]);

        return $new;
    }

    /**
     * @param $UserID
     * @param bool $for_save
     *
     * @return null| Model_News_New
     */
    public function getById($newId, $for_save = false)
    {
        $new = new  Model_News_New($this);

        if (!parent::_getOneByIndex($newId, $new, new Database_Main(), self::TABLE, self::INDEX, $for_save)) {
            return null;
        }

        return $new;
    }

    /**
     * @param string $alias
     * @param bool $for_save
     *
     * @return null| Model_News_New
     */
    public function getByAlias($alias, $for_save = false)
    {
        $new = new  Model_News_New($this);

        if (!parent::_getOneByIndex($alias, $new, new Database_Main(), self::TABLE, self::INDEX_ALIAS, $for_save)) {
            return null;
        }

        return $new;
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
     * @param Model_News_New $new
     *
     * @return bool|int|null
     */
    public function save(Model_News_New $new)
    {
        if ($new->newId) {
            $result = parent::_saveDifferencesByIndex($new->newId, $new, new Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($new, new Database_Main(), self::TABLE, self::INDEX);
            $new->newId = $result;
        }

        return $result;
    }

    public function delete(Model_News_New $new)
    {
        $photo = $new->getPhoto();

        if (isset($photo['small'])) {
            unlink(IMAGES_PATH . 'news/small/' . $photo['small']['path']);
        }

        if (isset($photo['big'])) {
            unlink(IMAGES_PATH . 'news/big/' . $photo['big']['path']);
        }

        return parent::_deleteByIndex($new->newId, new Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return Lib_ORM_Query
     */
    public function query()
    {
        $query = new Lib_ORM_Query(new  Model_News_New($this), new Database_Main(), self::TABLE);

        return $query;
    }
}
