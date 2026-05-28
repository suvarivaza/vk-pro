<?php

namespace Service\Users;

/**
 * @property Model_Factory $factory
 */
class Model_Notifications extends \Lib_ORM
{
    public const TYPE_DAY3 = 3;
    public const TYPE_DAY1 = 1;
    public const TYPE_DAY0 = 0;

    public const TABLE = 'notifications';
    public const PRIMARY = 'PRIMARY';
    public const INDEX_USERID_SERVICE_STATUS = 'userId_service_status';

    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNew()
    {
        $notification = new Model_Notifications_Notification($this);
        $notification->userId = 0;
        $notification->status = 0;
        $notification->title = '';
        $notification->service = '';

        return $notification;
    }

    /**
     * @param $notificationId
     * @param bool $for_save
     *
     * @return null|  Model_Notifications_Notification
     */
    public function getById($notificationId, $for_save = false)
    {
        $notification = new  Model_Notifications_Notification($this);

        if (!parent::_getOneByIndex($notificationId, $notification, new \Database_Main(), self::TABLE, self::PRIMARY,
            $for_save)) {
            return null;
        }

        return $notification;
    }

    /**
     * @param $userId
     * @param $service
     * @param $status
     * @param int $limit
     *
     * @return \Lib_ORM_Object[]
     *
     * @throws \Lib_Exception_Logic_Backtraced
     */
    public function getListByUserIdServiceStatus($userId, $service, $status, $limit = 1000)
    {
        $notification = new   Model_Notifications_Notification($this);
        $list = parent::_getCollectionByIndex([$userId, $service, $status], $notification, new \Database_Main(),
            self::TABLE, self::INDEX_USERID_SERVICE_STATUS, $for_save, $limit);

        return $list;
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
     * @param Model_Notifications_Notification $task
     *
     * @return bool|int|null
     */
    public function save(Model_Notifications_Notification $notification)
    {
        if ($notification->notificationId) {
            $result = parent::_saveDifferencesByIndex($notification->notificationId, $notification,
                new \Database_Main(), self::TABLE, self::PRIMARY);
        } else {
            $result = parent::_insert($notification, new \Database_Main(), self::TABLE, self::PRIMARY);
            $notification->notificationId = $result;
        }

        return $result;
    }

    public function delete(Model_Notifications_Notification $notification)
    {
        return parent::_deleteByIndex($notification->notificationId, new \Database_Main(), self::TABLE, self::PRIMARY);
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new   Model_Notifications_Notification($this), new \Database_Main(), self::TABLE);

        return $query;
    }
}
