<?php

/**
 * Фабрика объектов Lib_HSocket_Socket
 *
 * @link https://github.com/ahiguti/HandlerSocket-Plugin-for-MySQL/blob/master/docs-en/protocol.en.txt
 * @link http://code.google.com/p/php-handlersocket/
 */
class Lib_HSocket_Factory
{
    /**
     * Кодировка БД по умолчанию
     *
     * @var string
     */
    public const default_encoding = 'utf8';

    /**
     * Кодировка исходного кода
     *
     * @var string
     */
    public const source_encoding = 'utf8';

    /**
     * Массив закешированных объектов Lib_HSocket_Socket
     *
     * @var Lib_HSocket_Socket[]
     */
    private static $_cache = [];

    /**
     * Возвращает инстанс
     *
     * @static
     *
     * @return Lib_HSocket_Socket
     */
    public static function GetInstance(\Database $db)
    {
        if (!isset(self::$_cache[$db->name])) {
            self::$_cache[$db->name] = new Lib_HSocket_Socket($db, true);
        }

        return self::$_cache[$db->name];
    }

    /**
     * Закрывает все или один сокет и очищает кэш
     *
     * @static
     *
     * @param string $db
     */
    public static function Flush($db = '')
    {
        if ('' !== $db) {
            if (isset(self::$_cache[$db])) {
                self::$_cache[$db]->close();
                unset(self::$_cache[$db]);
            }
        } else {
            foreach (array_keys(self::$_cache) as $dbName) {
                self::$_cache[$dbName]->close();
            }

            self::$_cache = [];
        }
    }
}
