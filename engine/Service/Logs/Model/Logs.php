<?php

namespace Service\Logs;

use Database_Logs;
use Lib_Exception_UnknownProperty_Backtraced;
use Lib_ORM;
use Lib_ORM_Query;

/**
 * @property Model_Factory $factory
 * @property \Service\Users\Model_Factory $factoryUser
 */
class Model_Logs extends Lib_ORM
{
    /** @var Model_Factory */
    protected $_factory;

    private $_factoryUser;

    private $_date = null;

    /**
     * @param Model_Factory $factory
     * @param int $date
     */
    public function __construct(Model_Factory $factory, $date = 0)
    {
        $this->_factory = $factory;
        $this->_date = date('Y', $date) . '_' . date('m', $date);
    }

    /**
     * @param $logId
     * @param bool $for_save
     *
     * @return Model_Logs_Log|null
     */
    public function getById($logId, $for_save = false)
    {
        $obj = new Model_Logs_Log($this);

        if (!parent::_getOneByIndex($logId, $obj, new Database_Logs(), Model_Logs_Log::TABLE . $this->_date,
            Model_Logs_Log::PRIMARY, $for_save)) {
            return null;
        }

        return $obj;
    }

    /**
     * @param $action
     * @param $for_save
     *
     * @return \Lib_ORM_Object[]|Model_Logs_Log[]
     */
    public function getByAction($action, $for_save)
    {
        $obj = new Model_Logs_Log($this);

        return parent::_getCollectionByIndex($action, $obj, new Database_Logs(), Model_Logs_Log::TABLE . $this->_date,
            Model_Logs_Log::INDEX_ACTION, $for_save);
    }

    /**
     * @param $objectId
     * @param $for_save
     *
     * @return \Lib_ORM_Object[]|Model_Logs_Log[]
     * @throws \Lib_Exception_Logic_Backtraced
     */
    public function getByObject($objectId, $for_save)
    {
        $obj = new Model_Logs_Log($this);

        return parent::_getCollectionByIndex($objectId, $obj, new Database_Logs(),
            Model_Logs_Log::TABLE . $this->_date, Model_Logs_Log::INDEX_OBJECT, $for_save);
    }

    public function __get($name)
    {
        switch ($name) {
            case 'factory':
                return $this->_factory;

            case 'factoryUser':
                if (null === $this->_factoryUser) {
                    $this->_factoryUser = new \Service\Users\Model_Factory();
                }

                return $this->_factoryUser;
        }

        throw new Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }

    /**
     * @return Lib_ORM_Query
     */
    public function query()
    {
        return new Lib_ORM_Query(new Model_Logs_Log($this), new Database_Logs(),
            Model_Logs_Log::TABLE . $this->_date);
    }

    /**
     * @param $action
     * @param $objectId
     * @param $userId
     * @param array $params
     * @param int $priceId
     * @param int $itemId
     * @param int $statusId
     */
    public function Log($action, $objectId, $userId, $params = [])
    {
        $log = $this->getNewItem();
        $log->action = $action;
        $log->title = Model_Config::$Actions[$action];
        $log->date = time();
        $log->objectId = $objectId;

        if ($userId) {
            $log->userId = $userId;
        }
        $log->ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $log->url = $_SERVER['DOCUMENT_URI'] ?? '';
        $log->setParams($params);

        $this->save($log);
    }

    public function getNewItem()
    {
        $item = new Model_Logs_Log($this);
        $item->domainId = 1;
        $item->userId = 0;
        $item->priceId = 0;
        $item->itemId = 0;
        $item->statusId = 0;

        return $item;
    }

    /**
     * @param Model_Logs_Log $log
     *
     * @return bool|int|null
     */
    public function save(Model_Logs_Log $log)
    {

        if (!$log->logId) {
            $result = parent::_insert($log, new Database_Logs(), Model_Logs_Log::TABLE . $this->_date,
                Model_Logs_Log::PRIMARY);
            $log->logId = $result;
        } else {
            $result = parent::_saveDifferencesByIndex($log->logId, $log, new Database_Logs(),
                Model_Logs_Log::TABLE . $this->_date, Model_Logs_Log::PRIMARY);
        }


        return $result;
    }

    public function delete(Model_Logs_Log $log)
    {
        return parent::_deleteByIndex($log->logId, new Database_Logs(), Model_Logs_Log::TABLE . $this->_date,
            Model_Logs_Log::PRIMARY);
    }
}
