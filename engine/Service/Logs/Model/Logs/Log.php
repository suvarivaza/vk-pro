<?php

namespace Service\Logs;

use Lib_ORM_Object;

/**
 * Class Model_Logs_Log
 *
 * @package Service\Logs
 *
 * @property int $logId
 * @property int $domainId
 * @property string $action
 * @property string $title
 * @property string $url
 * @property int $date
 * @property string $objectId
 * @property string $priceId
 * @property string $itemId
 * @property string $statusId
 * @property string $userId
 * @property string $ip
 * @property string $params
 */
class Model_Logs_Log extends Lib_ORM_Object
{
    public const TABLE = 'log_';

    public const PRIMARY = 'PRIMARY';
    public const INDEX_ACTION = 'i_action';
    public const INDEX_OBJECT = 'i_object';
    private static $_PropertiesTypes;
    /** @var Model_Logs */
    protected $_factory;

    public function __construct(Model_Logs $factory)
    {
        parent::__construct();
        $this->_factory = $factory;
    }

    /**
     * @return array
     */
    public static function GetPropertiesTypes()
    {
        if (null === self::$_PropertiesTypes) {
            self::$_PropertiesTypes = [
                'logId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED | self::FLAG_AUTOINCREMENT,
                'domainId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'action' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'title' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'url' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'date' => self::TYPE_TIMESTAMP | self::FLAG_NOT_NULL,
                'objectId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'priceId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'itemId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'statusId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'userId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'ip' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'params' => self::TYPE_STRING | self::FLAG_NOT_NULL,
            ];
        }

        return self::$_PropertiesTypes;
    }

    public function getParams()
    {
        $params = json_decode($this->params, true);

        return $params;
    }

    public function setParams($params)
    {

        $this->params = json_encode($params, JSON_UNESCAPED_UNICODE);
    }
}
