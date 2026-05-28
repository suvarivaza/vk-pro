<?php

/**
 * Исключение при обращении к несуществующему свойству класса.
 * Вызывается отсутствующее свойство класса с перегруженными свойствами.
 */
class Lib_Exception_UnknownProperty extends Lib_Exception
{
    /**
     * @param string $propertyName
     * @param string $className
     */
    public function __construct($propertyName, $className)
    {
        parent::__construct('Trying to access to unknown property ' . $propertyName . ' in class ' . $className);
    }
}
