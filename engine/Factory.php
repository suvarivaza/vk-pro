<?php

/*
 * Здесь непонятно. Похоже Catalog  Documents Logistic Warehouses таких моделей нет!!! Разобраться!
 */


/**
 * Class Service_Factory
 *
 * @property \Service\Catalog\Model_Factory $factoryCatalog
 * @property \Service\Documents\Model_Factory $factoryDocuments
 * @property \Service\Logistic\Model_Factory $factoryLogistic
 * @property \Service\Messages\Model_Factory $factoryMessages
 * @property \Service\Orders\Model_Factory $factoryOrders
 * @property \Service\Users\Model_Factory $factoryUsers
 * @property \Service\Warehouses\Model_Factory $factoryWarehouses
 */
class Factory
{
    private static $_factoryCatalog = null;
    private static $_factoryDocuments = null;
    private static $_factoryLogistic = null;
    private static $_factoryMessages = null;
    private static $_factoryOrders = null;
    private static $_factoryUsers = null;
    private static $_factoryWarehouses = null;

    public function __get($name)
    {
        switch ($name) {
            case 'factoryCatalog':
                if (self::$_factoryCatalog === null) {
                    self::$_factoryCatalog = new \Service\Catalog\Model_Factory();
                }

            return self::$_factoryCatalog;
            case 'factoryDocuments':
                if (self::$_factoryDocuments === null) {
                    self::$_factoryDocuments = new \Service\Documents\Model_Factory();
                }

            return self::$_factoryDocuments;
            case 'factoryLogistic':
                if (self::$_factoryLogistic === null) {
                    self::$_factoryLogistic = new \Service\Logistic\Model_Factory();
                }

                return self::$_factoryDocuments;
            case 'factoryMessages':
                if (self::$_factoryMessages === null) {
                    self::$_factoryMessages = new \Service\Messages\Model_Factory();
                }

                return self::$_factoryMessages;
            case 'factoryOrders':
                if (self::$_factoryOrders === null) {
                    self::$_factoryOrders = new \Service\Orders\Model_Factory();
                }

                return self::$_factoryOrders;
            case 'factoryUsers':
                if (self::$_factoryUsers === null) {
                    self::$_factoryUsers = new \Service\Users\Model_Factory();
                }

                return self::$_factoryUsers;
            case 'factoryWarehouses':
                if (self::$_factoryWarehouses === null) {
                    self::$_factoryWarehouses = new \Service\Warehouses\Model_Factory();
                }

                return self::$_factoryWarehouses;
        }

        throw new \Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }
}
