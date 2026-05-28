<?php

namespace Service\Users;

/**
 * @property Model_Factory $factory
 */
class Model_Users_Bonuses extends \Lib_ORM
{
    public const TABLE = 'users_bonus';

    public const INDEX = 'PRIMARY';
    public const INDEX_USERID = 'i_userId_year_week_day';

    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $referrer = new Model_Users_Bonuses_Bonus($this);

        return $referrer;
    }

    /**
     * @param $userBonusId
     * @param bool $for_save
     *
     * @return Model_Users_Bonuses_Bonus|null
     */
    public function getById($userBonusId, $for_save = false)
    {
        $referrer = new Model_Users_Bonuses_Bonus($this);

        if (parent::_getOneByIndex($userBonusId, $referrer, new \Database_Main(), self::TABLE, self::INDEX,
            $for_save ? true : false)) {
            return $referrer;
        }

        return null;
    }

    /**
     * @param $userId
     * @param bool $for_save
     *
     * @return Model_Users_Bonuses_Bonus|null
     */
    public function getByUserId($userId, $year, $week, $day, $for_save = false)
    {
        $bonus = new Model_Users_Bonuses_Bonus($this);

        if (parent::_getOneByIndex([$userId, $year, $week, $day], $bonus, new \Database_Main(), self::TABLE,
            self::INDEX_USERID, $for_save ? true : false)) {
            return $bonus;
        }

        return null;
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
     * @param Model_Users_Bonuses_Bonus $userBonus
     *
     * @return bool|int|null
     */
    public function save(Model_Users_Bonuses_Bonus $userBonus)
    {
        if ($userBonus->userBonusId) {
            $result = parent::_saveDifferencesByIndex($userBonus->userBonusId, $userBonus, new \Database_Main(),
                self::TABLE, self::INDEX);
        } else {
            $result = parent::_insert($userBonus, new \Database_Main(), self::TABLE, self::INDEX);
            $userBonus->userBonusId = $result;
        }

        return $result;
    }

    public function delete(Model_Users_Bonuses_Bonus $userBonus)
    {
        return parent::_deleteByIndex($userBonus->userBonusId, new \Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new Model_Users_Bonuses_Bonus($this), new \Database_Main(), self::TABLE);

        return $query;
    }
}
