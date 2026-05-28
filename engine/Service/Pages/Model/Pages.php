<?php

namespace Service\Pages;

class Model_Pages extends \Lib_ORM
{
    public const TABLE = 'pages';

    public const PRIMARY = 'PRIMARY';
    public const INDEX_ALIAS = 'u_alias';

    /**
     * @param $pageId
     * @param bool $for_save
     *
     * @return Model_Pages_Page|null
     */
    public function GetPage($pageId, $for_save = false)
    {
        $query = $this->query();
        $query->filter->fieldValue('pageId', '=', $pageId);

        if ($for_save) {
            $it = $query->iteratorForSave();
        } else {
            $it = $query->iterator();
        }

        $item = $it->current();

        if (!$item) {
            return null;
        }

        return $item;
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        return new \Lib_ORM_Query(new Model_Pages_Page($this), new \Database_Main(), self::TABLE);
    }

    /**
     * @param $alias
     * @param bool $for_save
     *
     * @return Model_Pages_Page
     */
    public function GetPageByAlias($alias, $for_save = false)
    {
        $query = $this->query();
        $query->filter->fieldValue('alias', '=', $alias);

        if ($for_save) {
            $it = $query->iteratorForSave();
        } else {
            $it = $query->iterator();
        }

        $item = $it->current();

        if (!$item) {
            return null;
        }

        return $item;
    }

    public function getNew()
    {
        $page = new Model_Pages_Page($this);
        $page->parentId = 0;
        $page->strict = false;
        $page->dateCreate = time();
        $page->userId = 0;
        $page->lastUserId = 0;
        $page->lastDate = time();
        $page->title = '';
        $page->alias = '';
        $page->describe = '';
        $page->keywords = '';
        $page->text = '';
        $page->announce = false;
        $page->isArticle = false;
        $page->isNew = false;
        $page->photo = '';
        $page->count = 0;

        return $page;
    }

    public function save(Model_Pages_Page $page)
    {

        if ($page->pageId) {
            return parent::_saveDifferencesByIndex($page->pageId, $page, new \Database_Main(), self::TABLE,
                self::PRIMARY);
        } else {
            if ($id = parent::_insert($page, new \Database_Main(), self::TABLE)) {
                $page->pageId = $id;

                return true;
            } else {
                return false;
            }
        }
    }

    public function delete($pageId)
    {
        return parent::_deleteByIndex($pageId, new \Database_Main(), self::TABLE, self::PRIMARY);
    }
}
