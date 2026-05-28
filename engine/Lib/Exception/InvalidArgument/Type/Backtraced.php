<?php

/**
 * Исключение о неверном типе аргумента функции.
 * Надо использовать когда входные параметры не соответствуют спецификации.
 * Вместо имени файла пришла пустая строка или число или bool.
 * Версия с бэктрейсом.
 */
final class Lib_Exception_InvalidArgument_Type_Backtraced extends Lib_Exception_InvalidArgument_Type implements Lib_Exception_Backtrace_Interface
{
}
