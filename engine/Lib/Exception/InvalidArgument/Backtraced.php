<?php

/**
 * Исключение о неверном аргументе функции.
 * Надо использовать когда входные параметры не соответствуют спецификации.
 * Вместо имени файла пришла пустая строка или число или bool.
 * Версия с бэкстрейсом.
 */
final class Lib_Exception_InvalidArgument_Backtraced extends Lib_Exception_InvalidArgument implements Lib_Exception_Backtrace_Interface
{
}
