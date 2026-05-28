<?php

namespace Service\Users;

/**
 * @property Model_Factory $factory
 * @property Model_Users_Karma $karma
 * @property Model_Users_Balances $balance
 * @property Model_Users_Referrers $referrers
 * @property Model_Users_Requests $requests
 * @property Model_Users_Bonuses $bonuses
 */
class Model_Users extends \Lib_ORM
{
    public const TABLE = 'users';

    public const INDEX = 'PRIMARY';

    public const INDEX_TOKEN = 'i_token';
    public const INDEX_REFERRER_URL = 'i_referrerUrl';
    public const INDEX_LOGIN = 'i_login';
    public const INDEX_EMAIL = 'i_email';
    public const INDEX_LOGIN_PASSWORD = 'i_login_password';
    public const INDEX_EMAIL_PASSWORD = 'i_email_password';
    public const INDEX_UID = 'i_uid';

    /** @var Model_Factory */
    protected $_factory;

    /** @var Model_Users_Karma */
    protected $_karma = null;

    /** @var Model_Users_Balances */
    protected $_balance = null;

    /** @var Model_Users_Referrers */
    protected $_referrers = null;

    /** @var Model_Users_Requests */
    protected $_requests = null;

    /** @var Model_Users_Bonuses */
    protected $_bonuses = null;

    protected $_karmaMinus = [
        0 => -100.00,
        1 => -100.00,
        2 => -250.00,
        3 => -500.00,
    ];

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNewUser()
    {
        $user = new Model_Users_User($this);
        $user->userId = 0;
        $user->parentId = 0;
        $user->pParentId = 0;
        $user->ppParentId = 0;
        $user->uid = 0;
        $user->identity = '';
        $user->network = '';
        $user->lastName = '';
        $user->secondName = '';
        $user->country = '';
        $user->city = '';
        $user->profile = '';
        $user->login = '';
        $user->password = '';
        $user->token = '';
        $user->userType = 0;
        $user->name = '';
        $user->phone = '';
        $user->icq = '';
        $user->skype = '';
        $user->confirmed = 0;
        $user->restore = '';
        $user->dateCreate = time();
        $user->dateUpdate = $user->dateCreate;
        $user->visible = false;
        $user->scope = '';
        $user->balance = 0.0;
        $user->balanceRef = 0.0;
        $user->karma = 20.0;

        $user->pollsCountDay = 0;
        $user->pollsCountHour = 0;
        $user->pollsCount10Min = 0;
        $user->pollsCountMinute = 0;
        $user->joinCountDay = 0;
        $user->joinCountHour = 0;
        $user->joinCount10Min = 0;
        $user->joinCountMinute = 0;
        $user->friendsCountDay = 0;
        $user->friendsCountHour = 0;
        $user->friendsCount10Min = 0;
        $user->friendsCountMinute = 0;
        $user->likesCountDay = 0;
        $user->likesCountHour = 0;
        $user->likesCount10Min = 0;
        $user->likesCountMinute = 0;
        $user->repostsCountDay = 0;
        $user->repostsCountHour = 0;
        $user->repostsCount10Min = 0;
        $user->repostsCountMinute = 0;
        $user->commentsCountDay = 0;
        $user->commentsCountHour = 0;
        $user->commentsCount10Min = 0;
        $user->commentsCountMinute = 0;
        $user->token_require = false;

        $user->isRefferer = false;
        $user->referrerUrl = '';
        $user->qiwi = '';
        $user->qiwi_prefix = '';

        $user->karmaMinus = 0;
        $user->isFree = 0;

        $user->ban = false;
        $user->bad = 0;
        $user->banDate = null;

        $user->bonus = 1;
        $user->isBot = 0;
        $user->age_limits = 0;
        $user->badEmail = false;

        $user->setPhotos([]);

        return $user;
    }

    /**
     * @param string $login
     * @param bool $for_save
     *
     * @return Model_Users_User
     */
    public function getByLogin($login, $for_save = false)
    {
        $user = new Model_Users_User($this);

        if (!parent::_getOneByIndex($login, $user, new \Database_Main(), self::TABLE, self::INDEX_LOGIN, $for_save)) {
            return null;
        }

        return $user;
    }

    /**
     * @param string $email
     * @param bool $for_save
     *
     * @return Model_Users_User
     */
    public function getByEmail($email, $for_save = false)
    {
        $user = new Model_Users_User($this);

        if (!parent::_getOneByIndex($email, $user, new \Database_Main(), self::TABLE, self::INDEX_EMAIL, $for_save)) {
            return null;
        }

        return $user;
    }

    /**
     * @param $email
     * @param bool $for_save
     *
     * @return Model_Users_User
     *
     * @throws \Lib_Exception_InvalidArgument_Backtraced
     * @throws \Lib_Exception_Logic_Backtraced
     */
    public function getByEmailNew($email, $for_save = false)
    {
        $query = $this->query();
        $query->filter->fieldValue('email', 'LIKE', $email);

        if ($for_save) {
            $it = $query->iteratorForSave();
        } else {
            $it = $query->iterator();
        }
        $user = $it->current();

        if (!$user) {
            return null;
        }

        return $user;
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new Model_Users_User($this), new \Database_Main(), self::TABLE);

        return $query;
    }

    /**
     * @param string $login
     * @param string $password
     * @param bool $for_save
     *
     * @return Model_Users_User
     */
    public function getUserByLoginPass($login, $password, $for_save = false)
    {
        $user = new Model_Users_User($this);

        if (!parent::_getOneByIndex([$login, $password], $user, new \Database_Main(), self::TABLE,
            self::INDEX_LOGIN_PASSWORD, $for_save)) {
            return null;
        }

        return $user;
    }

    /**
     * @param string $email
     * @param string $password
     * @param bool $for_save
     *
     * @return Model_Users_User
     */
    public function getUserByEmailPass($email, $password, $for_save = false)
    {
        $user = new Model_Users_User($this);

        if (!parent::_getOneByIndex([$email, $password], $user, new \Database_Main(), self::TABLE,
            self::INDEX_EMAIL_PASSWORD, $for_save)) {
            return null;
        }

        return $user;
    }

    /**
     * @param string $token
     * @param bool $for_save
     *
     * @return Model_Users_User|null
     */
    public function getByToken($token, $for_save = false)
    {
        $user = new Model_Users_User($this);

        if (!parent::_getOneByIndex($token, $user, new \Database_Main(), self::TABLE, self::INDEX_TOKEN, $for_save)) {
            return null;
        }

        return $user;
    }

    /**
     * @param string $referrerUrl
     * @param bool $for_save
     *
     * @return Model_Users_User|null
     */
    public function getByReferrerUrl($referrerUrl, $for_save = false)
    {
        $user = new Model_Users_User($this);

        if (!parent::_getOneByIndex($referrerUrl, $user, new \Database_Main(), self::TABLE, self::INDEX_REFERRER_URL,
            $for_save)) {
            return null;
        }

        return $user;
    }

    /**
     * @param string $uid
     * @param bool $for_save
     *
     * @return Model_Users_User|null
     */
    public function getByUid($uid, $for_save = false)
    {
        $user = new Model_Users_User($this);

        if (!parent::_getOneByIndex($uid, $user, new \Database_Main(), self::TABLE, self::INDEX_UID, $for_save)) {
            return null;
        }

        return $user;
    }

    public function getRandomToken()
    {
        $sql = "SELECT `access_token` FROM `users` WHERE `access_token` != '' AND LENGTH(`access_token`) = 85 AND `access_token_expire` IS NULL ORDER BY RAND() LIMIT 1";
        $res = $this->factory->db->query($sql);
        $row = $res->fetch_assoc();
        error_log(print_r($row, true));
        return $row['access_token'];
    }

    public function __get($name)
    {
        switch ($name) {
            case 'factory':
                return $this->_factory;
            case 'karma':
                if ($this->_karma === null) {
                    $this->_karma = new Model_Users_Karma($this->factory, time());
                }

                return $this->_karma;
            case 'balance':
                if ($this->_balance === null) {
                    $this->_balance = new Model_Users_Balances($this->factory, time());
                }

                return $this->_balance;
            case 'referrers':
                if ($this->_referrers === null) {
                    $this->_referrers = new Model_Users_Referrers($this->factory);
                }

                return $this->_referrers;
            case 'requests':
                if ($this->_requests === null) {
                    $this->_requests = new Model_Users_Requests($this->factory);
                }

                return $this->_requests;
            case 'bonuses':
                if ($this->_bonuses === null) {
                    $this->_bonuses = new Model_Users_Bonuses($this->factory);
                }

                return $this->_bonuses;
        }

        throw new \Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }

    /**
     * @param Model_Users_User $user
     *
     * @return bool|int|null
     */
    public function save(Model_Users_User $user)
    {
        if ($user->userId) {
            $shadow = $user->getShadow();

            if ($shadow->karma > 0 && $user->karma < 0) {
                $user->karmaMinus++;

                if ($user->karmaMinus >= 3) {
                    $user->karmaMinus = 3;
                }
            }

            if ($user->karma < $this->_karmaMinus[$user->karmaMinus]) {
                $user->karma = $this->_karmaMinus[$user->karmaMinus];
            }

            $result = parent::_saveDifferencesByIndex($user->userId, $user, new \Database_Main(), self::TABLE,
                self::INDEX);
        } else {
            $result = parent::_insert($user, new \Database_Main(), self::TABLE, self::INDEX);
            $user->userId = $result;
        }

        return $result;
    }

    public function delete($userId)
    {
        return parent::_deleteByIndex($userId, new \Database_Main(), self::TABLE, self::INDEX);
    }

    public function getCountTargeting(
        $sex,
        $ageFrom,
        $ageTo,
        $city,
        $relation,
        $avatarCount,
        $filled,
        $pageAge,
        $followersCount,
        $interestingPage,
        $frequencyPost,
        $karma
    ) {
        $sql = 'SELECT COUNT(`userId`) FROM `' . self::TABLE . '`';

        if ($sex > 0) {
            $where[] = '`sex` = ' . $sex;
        }

        if ($ageFrom > 0) {
            $where[] = '`age` >= ' . $ageFrom;
        }

        if ($ageTo > 0) {
            $where[] = '`age` <= ' . $ageTo;
        }

        if ($city > 0) {
            $where[] = '`cityId` = ' . $city;
        }

        if ($relation > 0) {
            $where[] = '`relation` = ' . $relation;
        }

        if ($avatarCount > 0) {
            $where[] = '`avatarCount` >= ' . $avatarCount;
        }

        if ($filled > 0) {
            $where[] = '`partCount` >= ' . $filled;
        }

        if ($pageAge > 0) {
            $where[] = '`pageAge` >= ' . $pageAge;
        }

        if ($followersCount > 0) {
            $where[] = '`followersCount` >= ' . $followersCount;
        }

        if ($interestingPage > 0) {
            $where[] = '`pagesCount` <= ' . $interestingPage;
        }

        if ($frequencyPost > 0) {
            $where[] = '`frequency` <= ' . $frequencyPost;
        }

        if ($karma > 0) {
            $where[] = '`karma` >= ' . $karma;
        }

        if (count($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
            $res = $this->factory->db->query($sql);
            $row = $res->fetch_row();

            return $row[0];
        }

        return null;
    }

    public function getCountsByMonth($from)
    {
        $sql = 'SELECT MONTH(`dateCreate`) as `month`, COUNT(`userId`) as `count` FROM `' . self::TABLE . "` WHERE `dateCreate` > '" . date('Y-m-d',
                $from) . "' GROUP BY `month`";

        $res = $this->factory->db->query($sql);
        $row = $res->fetch_row();

        return $row[0];
    }

    public function getCountTotal()
    {
        $sql = 'SELECT COUNT(`userId`) FROM `' . self::TABLE . '`';
        $res = $this->factory->db->query($sql);

        $row = $res->fetch_row();

        return $row[0];
    }

    public function getCountTotalBan()
    {
        $sql = 'SELECT COUNT(`userId`) FROM `' . self::TABLE . '` WHERE `ban` = 1';
        $res = $this->factory->db->query($sql);

        $row = $res->fetch_row();

        return $row[0];
    }

    public function getCountTotalBad()
    {
        $sql = 'SELECT COUNT(`userId`) FROM `' . self::TABLE . '` WHERE `bad` > 1';
        $res = $this->factory->db->query($sql);

        $row = $res->fetch_row();

        return $row[0];
    }

    public function getCountTotalOnline()
    {
        $time = time() - (10 * 60);
        $sql = 'SELECT COUNT(`userId`) FROM `' . self::TABLE . "` WHERE `lastLogin` > '" . date('Y-m-d H:i:s',
                $time) . "'";
        $res = $this->factory->db->query($sql);

        $row = $res->fetch_row();

        return $row[0];
    }

    public function getStatByDay($from = null, $to = null, $bad = false, $ban = false)
    {
        if ($from === null) {
            $from = strtotime('-30 DAY');
        }

        if ($to === null) {
            $to = time();
        }

        $to = strtotime('+1 DAY', $to);

        $sql = "SELECT DATE_FORMAT(`dateCreate`, '%Y-%m-%d') as `day`, COUNT(`userId`) as `count` FROM `" . self::TABLE . '`';
        $sql .= " WHERE `dateCreate` > '" . date('Y-m-d', $from) . "'";
        $sql .= " AND `dateCreate` < '" . date('Y-m-d', $to) . "'";

        if ($bad) {
            $sql .= ' AND `bad` > 0';
        }

        if ($ban) {
            $sql .= ' AND `ban` = 1';
        }

        $sql .= ' GROUP BY `day` ORDER BY `day`';
        $res = $this->factory->db->query($sql);

        $list = [];

        while ($row = $res->fetch_assoc()) {
            $list[$row['day']] = $row['count'];
        }

        return $list;
    }

    public function getCities()
    {
        $sql = 'SELECT `cityId`, `city`, COUNT(`userId`) as `count` FROM `' . self::TABLE . '` GROUP BY `cityId` ORDER BY `count` DESC';
        $db = \Lib_DB_Factory::GetInstance(new \Database_Main());
        $res = $db->query($sql);
        \Lib_DB_Factory::Flush();
        $list = [];

        while ($row = $res->fetch_assoc()) {
            $list[] = [
                'cityId' => $row['cityId'],
                'title' => $row['city'],
                'count' => $row['count'],
            ];
        }

        return $list;
    }

    public function getCountries()
    {
        $sql = 'SELECT `countryId`, `country`, COUNT(`userId`) as `count` FROM `' . self::TABLE . '` GROUP BY `countryId` ORDER BY `count` DESC';
        $db = \Lib_DB_Factory::GetInstance(new \Database_Main());
        $res = $db->query($sql);
        \Lib_DB_Factory::Flush();
        $list = [];

        while ($row = $res->fetch_assoc()) {
            $list[] = [
                'countryId' => $row['countryId'],
                'title' => $row['country'],
                'count' => $row['count'],
            ];
        }

        return $list;
    }

    public function clearLimits($period, $polls, $join, $friends, $likes, $reposts, $comments)
    {
        $sql = 'UPDATE `users` SET ';
        $sql .= '`' . $polls . $period . '` = 0, ';
        $sql .= '`' . $join . $period . '` = 0, ';
        $sql .= '`' . $friends . $period . '` = 0, ';
        $sql .= '`' . $likes . $period . '` = 0, ';
        $sql .= '`' . $reposts . $period . '` = 0, ';
        $sql .= '`' . $comments . $period . '` = 0';
        $db = \Lib_DB_Factory::GetInstance(new \Database_Main());
        $res = $db->query($sql);
        \Lib_DB_Factory::Flush();

        return true;
    }

    public function getPaidUsers()
    {
        \Lib_DB_Factory::Flush();
        \Lib_HSocket_Factory::Flush();

        $factoryOrders = new \Service\Orders\Model_Factory();
        $sql = "select DISTINCT(`u`.`userId`) FROM `users` as `u` LEFT JOIN `tasks` as `t` ON `u`.`userId` = `t`.`userId` WHERE `u`.`email` != '' AND `t`.`userId` IS NOT NULL;";
        $db = \Lib_DB_Factory::GetInstance(new \Database_Main());
        $res = $db->query($sql);
        $list = [];

        while ($row = $res->fetch_row()) {
            $user = $this->getById($row[0]);
            $orders = $factoryOrders->orders->getByUserId($user->userId);

            if (count($orders)) {
                $list[] = $user;
            }
        }

        \Lib_DB_Factory::Flush();
        \Lib_HSocket_Factory::Flush();

        return $list;
    }

    /**
     * @param $UserID
     * @param bool $for_save
     *
     * @return Model_Users_User|null
     *
     * @throws \Lib_Exception_Logic_Backtraced
     */
    public function getById($UserID, $for_save = false)
    {
        $user = new Model_Users_User($this);


        if (!parent::_getOneByIndex($UserID, $user, new \Database_Main(), self::TABLE, self::INDEX, $for_save)) {
            return null;
        }

        //var_dump($user); die;

        return $user;

    }

    public function getTaskUsers()
    {
        \Lib_DB_Factory::Flush();
        \Lib_HSocket_Factory::Flush();

        $factoryOrders = new \Service\Orders\Model_Factory();
        $sql = "select DISTINCT(`u`.`userId`) FROM `users` as `u` LEFT JOIN `tasks` as `t` ON `u`.`userId` = `t`.`userId` WHERE `u`.`email` != '' AND `t`.`userId` IS NOT NULL;";
        $db = \Lib_DB_Factory::GetInstance(new \Database_Main());
        $res = $db->query($sql);
        $list = [];

        while ($row = $res->fetch_row()) {
            $user = $this->getById($row[0]);
            $orders = $factoryOrders->orders->getByUserId($user->userId);

            if (!count($orders)) {
                $list[] = $user;
            }
        }

        \Lib_DB_Factory::Flush();
        \Lib_HSocket_Factory::Flush();

        return $list;
    }

    public function getTaskUsersDone()
    {
        \Lib_DB_Factory::Flush();
        \Lib_HSocket_Factory::Flush();

        $sql = "select DISTINCT(`u`.`userId`) FROM `users` as `u` LEFT JOIN `tasks` as `t` ON `u`.`userId` = `t`.`userId` WHERE `t`.`countRemain` = 0 AND `t`.`dateCreate` > NOW() - INTERVAL 1 DAY AND `u`.`email` != '' AND `t`.`userId` IS NOT NULL;";
        $db = \Lib_DB_Factory::GetInstance(new \Database_Main());
        $res = $db->query($sql);
        $list = [];

        while ($row = $res->fetch_row()) {
            $user = $this->getById($row[0]);
            $list[] = $user;
        }

        \Lib_DB_Factory::Flush();
        \Lib_HSocket_Factory::Flush();

        return $list;
    }

    public function getNewUsers()
    {
        $user = new Model_Users_User($this);
        $fields = $user->GetPropertiesTypesNoFlags();

        $sql = "select `u`.* FROM `users` as `u` LEFT JOIN `tasks` as `t` ON `u`.`userId` = `t`.`userId` WHERE `u`.`email` != '' AND `t`.`userId` IS NULL";
        $db = \Lib_DB_Factory::GetInstance(new \Database_Main());
        $res = $db->query($sql);
        $list = [];

        while ($row = $res->fetch_assoc()) {
            $obj = clone $user;

            foreach ($fields as $name => $type) {
                if (!array_key_exists($name, $row)) {
                    throw new \Lib_Exception_Logic_Backtraced('Field ' . $name . ' is not present in row. For ' . get_class($obj));
                }

                if (($type == \Lib_ORM_Object::TYPE_DATETIME || $type == \Lib_ORM_Object::TYPE_TIMESTAMP) && null !== $row[$name]) {
                    call_user_func([$obj, '__set'], $name,
                        \Lib_TimeStamp::createFromFormat(\Lib_TimeStamp::MYSQL_FORMAT, $row[$name])->getTimestamp());
                } elseif ($type == \Lib_ORM_Object::TYPE_DATE && null !== $row[$name]) {
                    call_user_func([$obj, '__set'], $name,
                        \Lib_TimeStamp::createFromFormat('Y-m-d', $row[$name])->getTimestamp());
                } elseif ($type == \Lib_ORM_Object::TYPE_INT && null !== $row[$name]) {
                    call_user_func([$obj, '__set'], $name, (int) $row[$name]);
                } elseif ($type == \Lib_ORM_Object::TYPE_BOOL && null !== $row[$name]) {
                    call_user_func([$obj, '__set'], $name, (bool) $row[$name]);
                } elseif ($type == \Lib_ORM_Object::TYPE_FLOAT && null !== $row[$name]) {
                    call_user_func([$obj, '__set'], $name, (float) $row[$name]);
                } else {
                    call_user_func([$obj, '__set'], $name, $row[$name]);
                }
            }
            $list[] = $obj;
        }

        return $list;
    }
}
