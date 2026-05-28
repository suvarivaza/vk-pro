<?php

/**
 * Исключение о неверном типе аргумента функции.
 * Надо использовать когда входные параметры не соответствуют спецификации.
 * Вместо имени файла пришла пустая строка или число или bool.
 */
class Lib_Exception_InvalidArgument_Type extends Lib_Exception_InvalidArgument
{
    /**
     * @param mixed $argument
     * @param string $expectedType
     */
    public function __construct($argument, $expectedType)
    {
        parent::__construct('Invalid argument type. Got: ' . (is_object($argument) ? get_class($argument) : gettype($argument)) . '. Expected: ' . $expectedType);
    }
}
