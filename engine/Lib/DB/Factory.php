<?php

class Lib_DB_Factory
{
    /**
     * Массив закешированных линков
     *
     * @var \Lib_DB_Adapter[]
     */
    private static $_db_links = [];

    /**
     * Возвращает объект подключения к БД
     * Если $db_name sphinx-<name>, то хост становится <name>.w.sphinx
     *
     * @static
     *
     * @param Database $info
     * @param bool $longWait Увеличенное время простоя
     *
     * @return Lib_DB_Adapter
     */
    public static function GetInstance(\Database $info = null, $longWait = false)
    {
        if (!isset($info)) {
            throw new \Lib_Exception_UnknownProperty_Backtraced('info', 'Lib_DB_Factory');
        }

        if (!isset(self::$_db_links[$info->name])) {
            self::$_db_links[$info->name] = self::GetUncachedInstance($info, $longWait);
        }

        return self::$_db_links[$info->name];
    }

    /**
     * Возвращает некэшируемый объект подключения к БД
     *
     * @static
     *
     * @param bool $longWait Увеличенное время простоя
     *
     * @return Lib_DB_Adapter
     */
    public static function GetUncachedInstance(\Database $info = null, $longWait = false)
    {
        if ($info === null) {
            $info = self::GetInfo();
        }

        $adapter = new Lib_DB_Adapter(
            $info->host,
            $info->user,
            $info->pass,
            $info->name,
            $info->asArray(),
            $info->port,
            \Lib_DB_Adapter::DEFAULT_DRIVER,
            $longWait
        );

        return $adapter;
    }

    /**
     * Возвращает информацию о заданной БД
     *
     * @static
     *
     * @return \Database
     */
    public static function GetInfo($dbName = 'avtocity')
    {
        if ($dbName) {
            switch ($dbName) {
                case 'avtocity':
                    return new \Database_Avtocity();
                case 'tecdoc':
                    return new \Database_Tecdoc();
                case 'servermail':
                    return new \Database_Mails();
                case 'catalog':
                    return new \Database_Catalog();
            }
        }

        return new \Database_Avtocity();
    }

    /**
     * Установить плаг профайлинга
     *
     * @static
     *
     * @param $enabled
     */
    public static function SetProfiling($enabled)
    {
        Lib_DB_Adapter::SetProfiling($enabled);
    }

    /**
     * @static
     *
     * @return array
     */
    public static function GetProfiles()
    {
        return Lib_DB_Adapter::getProfiles();
    }

    /**
     * Закрывает все подключения ко всем БД
     *
     * @static
     */
    public static function Flush()
    {
        foreach (self::$_db_links as $id => $link) {
            $link->close();
            unset(self::$_db_links[$id]);
        }
        //unset( self::$_db_links );
    }
}
