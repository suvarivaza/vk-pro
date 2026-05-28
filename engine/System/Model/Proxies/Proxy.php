<?php

namespace System;

/**
 * Class Model_Proxies_Proxy
 *
 * @package Service\Catalog
 *
 * @property int $proxyId
 * @property string $ip
 * @property int $blocked
 * @property int $position
 */
class Model_Proxies_Proxy extends \Lib_ORM_Object
{
    public const TABLE = 'proxy';

    public const PRIMARY = 'PRIMARY';
    public const INDEX = 'i_blocked';

    /** @var Model_Proxies */
    protected $_factory;

    private static $_PropertiesTypes;

    public function __construct(Model_Proxies $factory)
    {
        parent::__construct();
        $this->_factory = $factory;
    }

    public function check()
    {
        return null;
    }

    /**
     * @return array
     */
    public static function GetPropertiesTypes()
    {
        if (null === self::$_PropertiesTypes) {
            self::$_PropertiesTypes = [
                'proxyId' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED | self::FLAG_AUTOINCREMENT,
                'ip' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'blocked' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'position' => self::TYPE_INT | self::FLAG_NOT_NULL,
            ];
        }

        return self::$_PropertiesTypes;
    }

    /**
     * @return Model_Proxies
     */
    public function getFactory()
    {
        return $this->_factory;
    }
}
