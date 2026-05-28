<?php

namespace Service\Grabber;

/**
 * @property Model_Factory $factory
 */
class Model_Posts extends \Lib_ORM
{
    public const TABLE = 'grabber_posts';

    public const INDEX = 'PRIMARY';
    public const INDEX_USERID = 'i_userId';
    public const INDEX_GROUPID = 'i_groupId';
    public const INDEX_GROUPID_ISPOST = 'i_groupId_isPost';

    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $post = new Model_Posts_Post($this);
        $post->isPostId = 0;
        $post->dateCreate = time();
        $post->setAttachments([]);
        $post->signature = false;

        return $post;
    }

    /**
     * @param $postId
     * @param bool $for_save
     *
     * @return null| Model_Posts_Post
     */
    public function getById($postId, $for_save = false)
    {
        $post = new  Model_Posts_Post($this);

        if (!parent::_getOneByIndex($postId, $post, new \Database_Main(), self::TABLE, self::INDEX, $for_save)) {
            return null;
        }

        return $post;
    }

    /**
     * @param $userId
     * @param bool $for_save
     *
     * @return Model_Posts_Post[]
     */
    public function getByUserId($userId, $for_save = false)
    {
        $post = new  Model_Posts_Post($this);

        return parent::_getCollectionByIndex($userId, $post, new \Database_Main(), self::TABLE, self::INDEX_USERID,
            $for_save);
    }

    /**
     * @param $groupId
     * @param bool $for_save
     *
     * @return Model_Posts_Post[]
     */
    public function getByGroupId($groupId, $for_save = false)
    {
        $post = new  Model_Posts_Post($this);

        return parent::_getCollectionByIndex($groupId, $post, new \Database_Main(), self::TABLE, self::INDEX_GROUPID,
            $for_save);
    }

    /**
     * @param $groupId
     * @param $isPost
     * @param bool $for_save
     *
     * @return Model_Posts_Post[]
     */
    public function getByGroupIdIsPost($groupId, $isPost = false, $for_save = false, $limit = 1000000)
    {
        $post = new  Model_Posts_Post($this);

        return parent::_getCollectionByIndex([$groupId, $isPost], $post, new \Database_Main(), self::TABLE,
            self::INDEX_GROUPID_ISPOST, $for_save, $limit);
    }

    public function getCounts($userId)
    {
        $sql = 'SELECT `isPost`, count(`postId`) FROM `' . self::TABLE . '` WHERE `userId` = ' . $userId;
        $sql .= ' GROUP BY `isPost`';
        $db = \Lib_DB_Factory::GetInstance(new \Database_Main());
        $res = $db->query($sql);

        $list = [];

        while ($row = $res->fetch_row()) {
            $list[$row[0]] = $row[1];
        }

        return $list;
    }

    public function getCountsBySource($sourceId, $isPost = false)
    {
        $sql = 'SELECT count(`postId`) FROM `' . self::TABLE . '` WHERE `sourceId` = ' . $sourceId;

        if ($isPost === true) {
            $sql .= ' AND `isPost` = 1';
        }
        $db = \Lib_DB_Factory::GetInstance(new \Database_Main());
        $res = $db->query($sql);
        $row = $res->fetch_row();

        return $row[0];
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
     * @param Model_Posts_Post $post
     *
     * @return bool|int|null
     */
    public function save(Model_Posts_Post $post)
    {
        if ($post->postId) {
            $result = parent::_saveDifferencesByIndex($post->postId, $post, new \Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($post, new \Database_Main(), self::TABLE, self::INDEX);
            $post->postId = $result;
        }

        return $result;
    }

    public function delete(Model_Posts_Post $post)
    {
        $attachments = $post->getAttachments();

        foreach ($attachments as $attachment) {
            if ($attachment['type'] != 'photo') {
                continue;
            }

            $path = IMAGES_PATH . 'posting/small/' . $attachment['small']['path'];
            @unlink($path);
            $path = IMAGES_PATH . 'posting/big/' . $attachment['big']['path'];
            @unlink($path);
        }

        return parent::_deleteByIndex($post->postId, new \Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new  Model_Posts_Post($this), new \Database_Main(), self::TABLE);

        return $query;
    }
}
