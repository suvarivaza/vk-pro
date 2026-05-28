<?php

namespace Service\Pages;

/**
 * Class Model_Pages_Page
 *
 * @package Service\Pages
 *
 * @property int $pageId
 * @property string $uniqueId
 * @property int $parentId
 * @property bool $strict
 * @property int $dateCreate
 * @property int $userId
 * @property int $lastUserId
 * @property int $lastDate
 * @property string $title
 * @property string $alias
 * @property string $describe
 * @property string $keywords
 * @property string $text
 * @property bool $announce
 * @property bool $isArticle
 * @property bool $isNew
 * @property string $photo
 * @property int $count
 */
class Model_Pages_Page extends \Lib_ORM_Object
{
    private $factory = null;

    public function __construct(Model_Pages $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return array
     */
    public static function GetPropertiesTypes()
    {
        return [
            'pageId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL | self::FLAG_AUTOINCREMENT,
            'parentId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
            'strict' => self::TYPE_BOOL | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
            'dateCreate' => self::TYPE_TIMESTAMP,
            'userId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
            'lastUserId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
            'lastDate' => self::TYPE_TIMESTAMP,
            'title' => self::TYPE_STRING | self::FLAG_NOT_NULL,
            'alias' => self::TYPE_STRING | self::FLAG_NOT_NULL,
            'describe' => self::TYPE_STRING | self::FLAG_NOT_NULL,
            'keywords' => self::TYPE_STRING | self::FLAG_NOT_NULL,
            'text' => self::TYPE_STRING | self::FLAG_NOT_NULL,
            'announce' => self::TYPE_BOOL | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
            'isArticle' => self::TYPE_BOOL | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
            'isNew' => self::TYPE_BOOL | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
            'photo' => self::TYPE_STRING | self::FLAG_NOT_NULL,
            'count' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
        ];
    }

    public function check()
    {
        $errors = [];

        if (!$this->title) {
            $errors[] = 'Необходимо указать заголовок';
        }

        if (!$this->alias) {
            $errors[] = 'Отсутствует ссылка на страницу';
        }

        if (!$this->describe) {
            $errors[] = 'Необходимо заполнить описание';
        }

        if (!$this->text) {
            $errors[] = 'Необходимо заполнить текст страницы';
        }

        $page = $this->factory->GetPageByAlias($this->alias);

        if ($page !== null && $page->pageId != $this->pageId) {
            $errors[] = 'Страница по такому адресу уже существует';
        }

        return $errors ?: false;
    }
}
