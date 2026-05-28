<?php

define ('MYSQL_ASSOC', 1);
define ('MYSQL_NUM', 2);
define ('MYSQL_BOTH', 3);

/**
 * Class Base
 * @property-read string $sock
 * @property-read string $host
 * @property-read string $name
 * @property-read string $user
 * @property-read string $pass
 * @property-read int $port
 */
abstract class Database
{
    protected $info = array();

    public function __get( $name )
    {
        switch ( $name )
        {
            case 'sock':
                return $this->info['sock'];
            case 'host':
                return $this->info['host'];
            case 'name':
                return $this->info['name'];
            case 'user':
                return $this->info['user'];
            case 'pass':
                return $this->info['pass'];
            case 'port':
                return $this->info['port'];
        }

        throw new \Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }

    public function asArray()
    {
        return $this->info;
    }
}