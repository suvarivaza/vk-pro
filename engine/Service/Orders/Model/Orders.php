<?php

namespace Service\Orders;

/**
 * @property Model_Factory $factory
 * @property Model_Orders_Packs $packs
 */
class Model_Orders extends \Lib_ORM
{
    public const TABLE = 'orders';

    public const INDEX = 'PRIMARY';
    public const INDEX_USERID = 'i_userId';
    public const INDEX_USERID_INVOICEID = 'i_userId_invoiceId';

    /** @var Model_Factory */
    protected $_factory;

    private $_packs = null;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $order = new Model_Orders_Order($this);
        $order->dateCreate = time();
        $order->isBuy = false;
        $order->isReferrer = false;
        $order->monthId = 0;
        $order->invoiceId = '';
        $order->token = '';
        $order->type = '';
        $order->giftId = 0;
        $order->isSlot = false;

        $order->isOrdered = false;
        $order->isAuto = false;
        $order->isPosting = false;
        $order->isGrabber = false;
        $order->isSpecial = false;
        $order->isBot = false;

        $order->isAutoMonth = 0;
        $order->isPostingMonth = 0;
        $order->isGrabberMonth = 0;
        $order->isSpecialMonth = 0;
        $order->isBotMonth = 0;

        return $order;
    }

    /**
     * @param orderId
     * @param bool $for_save
     *
     * @return null| Model_Orders_Order
     */
    public function getById($orderId, $for_save = false)
    {
        $order = new  Model_Orders_Order($this);

        if (!parent::_getOneByIndex($orderId, $order, new \Database_Main(), self::TABLE, self::INDEX, $for_save)) {
            return null;
        }

        return $order;
    }

    /**
     * @param userId
     * @param invoiceId
     * @param bool $for_save
     *
     * @return null| Model_Orders_Order
     */
    public function getByUserIdInvoiceId($userId, $invoiceId, $for_save = false)
    {
        $order = new  Model_Orders_Order($this);

        if (!parent::_getOneByIndex([$userId, $invoiceId], $order, new \Database_Main(), self::TABLE,
            self::INDEX_USERID_INVOICEID, $for_save)) {
            return null;
        }

        return $order;
    }

    /**
     * @param $userId
     * @param bool $for_save
     *
     * @return Model_Orders_Order[]
     */
    public function getByUserId($userId, $for_save = false, $limit = 100000)
    {
        $order = new  Model_Orders_Order($this);

        return parent::_getCollectionByIndex($userId, $order, new \Database_Main(), self::TABLE, self::INDEX_USERID,
            $for_save, $limit);
    }

    /**
     * @param Model_Orders_Order $order
     *
     * @return bool|int|null
     */
    public function save(Model_Orders_Order $order)
    {
        if ($order->orderId) {
            $result = parent::_saveDifferencesByIndex($order->orderId, $order, new \Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($order, new \Database_Main(), self::TABLE, self::INDEX);
            $order->orderId = $result;
        }

        return $result;
    }

    public function delete(Model_Orders_Order $order)
    {
        return parent::_deleteByIndex($order->orderId, new \Database_Main(), self::TABLE, self::INDEX);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new  Model_Orders_Order($this), new \Database_Main(), self::TABLE);

        return $query;
    }

    public function getCountsByMonth($from)
    {
        $sql = 'SELECT MONTH(`isBuyDate`) as `month`, COUNT(`orderId`) as `count`, SUM(`price`) as `sum` FROM `' . self::TABLE . "` WHERE `isBuyDate` > '" . date('Y-m-d',
                $from) . "' GROUP BY `month` ";
        $res = $this->factory->db->query($sql);

        $list = [];

        while ($row = $res->fetch_assoc()) {
            $list[$row['month']] = [
                'count' => $row['count'],
                'sum' => $row['sum'],
            ];
        }

        return $list;
    }

    public function getCountTotal()
    {
        $sql = 'SELECT COUNT(`orderId`) FROM `orders` WHERE `giftId` = 0';
        $res = $this->factory->db->query($sql);
        $row = $res->fetch_row();

        return $row[0];
    }

    public function __get($name)
    {
        switch ($name) {
            case 'factory':
                return $this->_factory;
            case 'packs':
                if ($this->_packs === null) {
                    $this->_packs = new Model_Orders_Packs($this->factory);
                }

                return $this->_packs;
        }

        throw new \Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }

    public function getStatByDay($from = null, $to = null, $done = false)
    {
        if ($from === null) {
            $from = strtotime('-30 DAY');
        }

        if ($to === null) {
            $to = time();
        }

        $to = strtotime('+1 DAY', $to);

        $sql = "SELECT DATE_FORMAT(`dateCreate`, '%Y-%m-%d') as `day`, COUNT(`orderId`) as `count` FROM `" . self::TABLE . '`';
        $sql .= " WHERE  `giftId` = 0 AND `dateCreate` > '" . date('Y-m-d', $from) . "'";
        $sql .= " AND `dateCreate` < '" . date('Y-m-d', $to) . "'";

        if ($done) {
            $sql .= ' AND `isBuy` = 1';
        }

        $sql .= ' GROUP BY `day` ORDER BY `day`';
        $res = $this->factory->db->query($sql);

        $list = [];

        while ($row = $res->fetch_assoc()) {
            $list[$row['day']] = $row['count'];
        }

        return $list;
    }

    public function getSumByDay($from = null, $to = null, $done = false)
    {
        if ($from === null) {
            $from = strtotime('-30 DAY');
        }

        if ($to === null) {
            $to = time();
        }

        $to = strtotime('+1 DAY', $to);

        $sql = "SELECT DATE_FORMAT(`dateCreate`, '%Y-%m-%d') as `day`, SUM(`price`) as `sum` FROM `" . self::TABLE . '`';
        $sql .= " WHERE  `giftId` = 0 AND `dateCreate` > '" . date('Y-m-d', $from) . "'";
        $sql .= " AND `dateCreate` < '" . date('Y-m-d', $to) . "'";

        if ($done) {
            $sql .= ' AND `isBuy` = 1';
        }

        $sql .= ' GROUP BY `day` ORDER BY `day`';
        $res = $this->factory->db->query($sql);

        $list = [];

        while ($row = $res->fetch_assoc()) {
            $list[$row['day']] = $row['sum'];
        }

        return $list;
    }

    public function getTotalsByMonths()
    {
        $from = strtotime('FIRST DAY OF -11 MONTH');

        $sql = "SELECT DATE_FORMAT(`dateCreate`, '%Y-%m') as `month`, SUM(`price`) as `sum` FROM `" . self::TABLE . '`';
        $sql .= " WHERE  `giftId` = 0 AND `dateCreate` > '" . date('Y-m-d', $from) . "'";
        $sql .= ' GROUP BY `month` ORDER BY `month`';

        $res = $this->factory->db->query($sql);

        $list = [];

        while ($row = $res->fetch_assoc()) {
            $list[$row['month']] = $row['sum'];
        }

        return $list;
    }

    public function getPacksByMonths()
    {
        $from = strtotime('FIRST DAY OF -11 MONTH');

        $sql = "SELECT DATE_FORMAT(`dateCreate`, '%Y-%m') as `month`, `packId`, SUM(`price`) as `sum`, COUNT(`orderId`) as `count` FROM `" . self::TABLE . '`';
        $sql .= " WHERE `packId` > 0 AND `giftId` = 0 AND `dateCreate` > '" . date('Y-m-d', $from) . "'";
        $sql .= ' GROUP BY `month`,`packId` ORDER BY `month`';

        $res = $this->factory->db->query($sql);

        $list = [];

        while ($row = $res->fetch_assoc()) {
            $list[$row['month']][$row['packId']] = ['sum' => $row['sum'], 'count' => $row['count']];
        }

        return $list;
    }

    public function getServicesByMonths()
    {
        $from = strtotime('FIRST DAY OF -11 MONTH');

        $sql = "SELECT DATE_FORMAT(`dateCreate`, '%Y-%m') as `month`,`isAuto`,`isPosting`,`isGrabber`,`isSpecial`,`isBot`, SUM(`price`) as `sum`, COUNT(`orderId`) as `count` FROM `" . self::TABLE . '`';
        $sql .= " WHERE `packId` = 0 AND `giftId` = 0 AND `dateCreate` > '" . date('Y-m-d', $from) . "'";
        $sql .= ' GROUP BY `month`,`isAuto`,`isPosting`,`isGrabber`,`isSpecial`,`isBot` ORDER BY `month`';

        $res = $this->factory->db->query($sql);

        $list = [];

        while ($row = $res->fetch_assoc()) {
            if ($row['isAuto']) {
                if (!isset($list[$row['month']]['isAuto'])) {
                    $list[$row['month']]['isAuto'] = ['sum' => $row['sum'], 'count' => $row['count']];
                } else {
                    $list[$row['month']]['isAuto']['sum'] += $row['sum'];
                    $list[$row['month']]['isAuto']['count'] += $row['count'];
                }
            }

            if ($row['isPosting']) {
                if (!isset($list[$row['month']]['isPosting'])) {
                    $list[$row['month']]['isPosting'] = ['sum' => $row['sum'], 'count' => $row['count']];
                } else {
                    $list[$row['month']]['isPosting']['sum'] += $row['sum'];
                    $list[$row['month']]['isPosting']['count'] += $row['count'];
                }
            }

            if ($row['isGrabber']) {
                if (!isset($list[$row['month']]['isGrabber'])) {
                    $list[$row['month']]['isGrabber'] = ['sum' => $row['sum'], 'count' => $row['count']];
                } else {
                    $list[$row['month']]['isGrabber']['sum'] += $row['sum'];
                    $list[$row['month']]['isGrabber']['count'] += $row['count'];
                }
            }

            if ($row['isSpecial']) {
                if (!isset($list[$row['month']]['isSpecial'])) {
                    $list[$row['month']]['isSpecial'] = ['sum' => $row['sum'], 'count' => $row['count']];
                } else {
                    $list[$row['month']]['isSpecial']['sum'] += $row['sum'];
                    $list[$row['month']]['isSpecial']['count'] += $row['count'];
                }
            }

            if ($row['isBot']) {
                if (!isset($list[$row['month']]['isBot'])) {
                    $list[$row['month']]['isBot'] = ['sum' => $row['sum'], 'count' => $row['count']];
                } else {
                    $list[$row['month']]['isBot']['sum'] += $row['sum'];
                    $list[$row['month']]['isBot']['count'] += $row['count'];
                }
            }
        }

        return $list;
    }

    public function getBalanceByMonths()
    {
        $from = strtotime('FIRST DAY OF -11 MONTH');

        $sql = "SELECT DATE_FORMAT(`dateCreate`, '%Y-%m') as `month`,`balance`, SUM(`price`) as `sum` FROM `" . self::TABLE . '`';
        $sql .= " WHERE `packId` = 0 AND `giftId` = 0 AND `isAuto` = 0 AND `isPosting` = 0 AND `isGrabber` = 0 AND `isSpecial` = 0 AND `balance` > 0 AND `dateCreate` > '" . date('Y-m-d',
                $from) . "'";
        $sql .= ' GROUP BY `month` ORDER BY `month`';

        $res = $this->factory->db->query($sql);

        $list = [];

        while ($row = $res->fetch_assoc()) {
            $list[$row['month']] = $row['sum'];
        }

        return $list;
    }

    public function getKarmaByMonths()
    {
        $from = strtotime('FIRST DAY OF -11 MONTH');

        $sql = "SELECT DATE_FORMAT(`dateCreate`, '%Y-%m') as `month`,`balance`, SUM(`price`) as `sum` FROM `" . self::TABLE . '`';
        $sql .= " WHERE `type` = 'karmaMinus' AND `packId` = 0 AND `giftId` = 0 AND `isAuto` = 0 AND `isPosting` = 0 AND `isGrabber` = 0 AND `isSpecial` = 0 AND `dateCreate` > '" . date('Y-m-d',
                $from) . "'";
        $sql .= ' GROUP BY `month` ORDER BY `month`';

        $res = $this->factory->db->query($sql);

        $list = [];

        while ($row = $res->fetch_assoc()) {
            $list[$row['month']] = $row['sum'];
        }

        return $list;
    }
}
