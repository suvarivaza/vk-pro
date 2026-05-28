<?php

namespace Service\Bot;

use Database_Main;
use Lib_Exception_Logic_Backtraced;
use Lib_Exception_UnknownProperty_Backtraced;
use Lib_ORM;
use Lib_ORM_Query;

/**
 * @property Model_Factory $factory
 */
class Model_Bots extends Lib_ORM
{
    public const TABLE = 'bots';

    public const INDEX = 'PRIMARY';
    public const INDEX_USERID = 'i_userId';

    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $bot = new Model_Bots_Bot($this);
        $bot->dateCreate = time();
        $bot->isBot = 0;
        $bot->isPro = false;

        return $bot;
    }

    /**
     * @param $botId
     * @param bool $for_save
     *
     * @return Model_Bots_Bot|null
     *
     * @throws Lib_Exception_Logic_Backtraced
     */
    public function getById($botId, $for_save = false)
    {
        $bot = new  Model_Bots_Bot($this);

        if (!parent::_getOneByIndex($botId, $bot, new Database_Main(), self::TABLE, self::INDEX, $for_save)) {
            return null;
        }

        return $bot;
    }

    /**
     * @param $userId
     * @param bool $for_save
     *
     * @return Model_Bots_Bot|null
     *
     * @throws Lib_Exception_Logic_Backtraced
     */
    public function getByUserId($userId, $for_save = false)
    {
        $bot = new  Model_Bots_Bot($this);

        if (!parent::_getOneByIndex($userId, $bot, new Database_Main(), self::TABLE, self::INDEX_USERID, $for_save)) {
            return null;
        }

        return $bot;
    }

    /**
     * @param Model_Bots_Bot $bot
     *
     * @return bool|int|null
     *
     * @throws Lib_Exception_Logic_Backtraced
     */
    public function save(Model_Bots_Bot $bot)
    {
        if ($bot->botId) {
            $result = parent::_saveDifferencesByIndex($bot->botId, $bot, new Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($bot, new Database_Main(), self::TABLE, self::INDEX);
            $bot->botId = $result;
        }

        return $result;
    }

    public function delete(Model_Bots_Bot $bot)
    {
        return parent::_deleteByIndex($bot->botId, new Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return Lib_ORM_Query
     */
    public function query()
    {
        $query = new Lib_ORM_Query(new  Model_Bots_Bot($this), new Database_Main(), self::TABLE);

        return $query;
    }

    public function getCounts()
    {
        $sql = 'SELECT COUNT(*) FROM `' . self::TABLE . '`';
        $res = $this->factory->db->query($sql);
        list($count) = $res->fetch_row();

        $sql = 'SELECT COUNT(*) FROM `' . self::TABLE . "` as `b` INNER JOIN `users` as `u` ON `b`.`userId` = `u`.`userId` WHERE (`b`.`isActive` = 1 AND `b`.`isBot` > 0 AND `b`.`isPro` = 0  AND `u`.`access_token` != '')";
        $res = $this->factory->db->query($sql);
        list($active) = $res->fetch_row();

        $sql = 'SELECT COUNT(*) FROM `' . self::TABLE . '` WHERE `isPro` = 1';
        $res = $this->factory->db->query($sql);
        list($pro) = $res->fetch_row();

        $sql = "select COUNT(`botId`) FROM `bots` as `b` INNER JOIN `users` as `u` ON `b`.`userId` = `u`.`userId` WHERE `u`.`access_token` = ''";
        $res = $this->factory->db->query($sql);
        list($work) = $res->fetch_row();

        return [
            'total' => $count,
            'active' => $active,
            'pro' => $pro,
            'work' => $work,
        ];
    }

    public function getStatByDay($from = null, $to = null)
    {
        if ($from === null) {
            $from = strtotime('-30 DAY');
        }

        if ($to === null) {
            $to = time();
        }

        $to = strtotime('+1 DAY', $to);

        $sql = "SELECT DATE_FORMAT(`isDoneDate`, '%Y-%m-%d') as `day`, COUNT(`taskId`) as `count` FROM `tasks_users`";
        $sql .= ' WHERE `isBot` = 1 AND `isDone` = 1';
        $sql .= " AND `isDoneDate` > '" . date('Y-m-d', $from) . "'";
        $sql .= " AND `isDoneDate` < '" . date('Y-m-d', $to) . "'";
        $sql .= ' GROUP BY `day` ORDER BY `day`';
        $res = $this->factory->db->query($sql);

        $list = [];

        while ($row = $res->fetch_assoc()) {
            $list[$row['day']] = $row['count'];
        }

        return $list;
    }

    public function getStatByDayType($from = null, $to = null)
    {
        if ($from === null) {
            $from = strtotime('-30 DAY');
        }

        if ($to === null) {
            $to = time();
        }

        $to = strtotime('+1 DAY', $to);

        $sql = "SELECT `type` as `type`, DATE_FORMAT(`isDoneDate`, '%Y-%m-%d') as `day`, COUNT(`taskId`) as `count` FROM `tasks_users`";
        $sql .= ' WHERE `isBot` = 1 AND `isDone` = 1';
        $sql .= " AND `isDoneDate` > '" . date('Y-m-d', $from) . "'";
        $sql .= " AND `isDoneDate` < '" . date('Y-m-d', $to) . "'";
        $sql .= ' GROUP BY `day`,`type` ORDER BY `day`';
        $res = $this->factory->db->query($sql);

        $list = [];

        while ($row = $res->fetch_assoc()) {
            $list[$row['day']] = $row['count'];
        }

        return $list;
    }

    public function __get($name)
    {
        switch ($name) {
            case 'factory':
                return $this->_factory;
        }

        throw new Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }
}
