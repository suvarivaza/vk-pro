<?php

namespace Service\Grabber;

/**
 * @property Model_Factory $factory
 */
class Model_Groups extends \Lib_ORM
{
    public const TABLE = 'grabber_groups';

    public const INDEX = 'PRIMARY';
    public const INDEX_USERID = 'i_userId';
    public const INDEX_GRABBERID = 'i_grabberId';

    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $group = new Model_Groups_Group($this);
        $group->dateCreate = time();
        $group->itemId = '';
        $group->interval = 0;
        $group->linkDelete = false;
        $group->hashtags = '';
        $group->hashtagsPos = 0;

        $group->timeLimit = false;
        $group->timeHourFrom = 0;
        $group->timeMinuteFrom = 0;
        $group->timeHourTo = 0;
        $group->timeMinuteTo = 0;
        $group->maxLength = 25;
        $group->photoInGroup = false;
        $group->adsLimit = false;
        $group->adsInterval = 0;
        $group->isWatermark = 0;
        $group->watermark = '';
        $group->watermarkOpacity = 0.5;
        $group->watermarkPos = 0;
        $group->watermarkMargin = 0;
        $group->watermarkMaxSize = 50;
        $group->watermarkText = '';
        $group->watermarkTextOpacity = 0.5;
        $group->watermarkTextPos = 0;
        $group->watermarkFont = '';
        $group->watermarkColor = '';
        $group->watermarkSize = 32;

        $group->isFree = false;
        $group->isFreeCount = 0;
        $group->wasFree = false;
        $group->random = false;

        $group->userActive = true;

        return $group;
    }

    /**
     * @param $groupId
     * @param bool $for_save
     *
     * @return null| Model_Groups_Group
     */
    public function getById($groupId, $for_save = false)
    {
        $group = new  Model_Groups_Group($this);

        if (!parent::_getOneByIndex($groupId, $group, new \Database_Main(), self::TABLE, self::INDEX, $for_save)) {
            return null;
        }

        return $group;
    }

    /**
     * @param $userId
     * @param bool $for_save
     *
     * @return Model_Groups_Group[]
     */
    public function getByUserId($userId, $for_save = false)
    {
        $group = new  Model_Groups_Group($this);

        return parent::_getCollectionByIndex($userId, $group, new \Database_Main(), self::TABLE, self::INDEX_USERID,
            $for_save);
    }

    /**
     * @param $grabberId
     * @param bool $for_save
     *
     * @return Model_Groups_Group[]
     */
    public function getByGrabberId($grabberId, $for_save = false)
    {
        $group = new  Model_Groups_Group($this);

        return parent::_getCollectionByIndex($grabberId, $group, new \Database_Main(), self::TABLE,
            self::INDEX_GRABBERID, $for_save);
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
     * @param Model_Groups_Group $group
     *
     * @return bool|int|null
     */
    public function save(Model_Groups_Group $group)
    {
        if ($group->groupId) {
            $result = parent::_saveDifferencesByIndex($group->groupId, $group, new \Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($group, new \Database_Main(), self::TABLE, self::INDEX);
            $group->groupId = $result;
        }

        return $result;
    }

    public function delete(Model_Groups_Group $group)
    {
        return parent::_deleteByIndex($group->groupId, new \Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new  Model_Groups_Group($this), new \Database_Main(), self::TABLE);

        return $query;
    }

    public function getCounts($free = false)
    {
        $sql = 'SELECT COUNT(DISTINCT(`userId`)) FROM `' . self::TABLE . '`';
        $sql .= ' WHERE `dateValid` > NOW() ';

        if ($free === true) {
            $sql .= ' AND `isFree` = 1 AND `isFreeCount` > 0';
        } else {
            $sql .= ' AND NOT (`isFree` = 1 AND `isFreeCount` = 0)';
        }

        $res = $this->factory->db->query($sql);

        $row = $res->fetch_row();

        return $row[0];
    }
}
