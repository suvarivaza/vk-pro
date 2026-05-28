<?php

/**
 * Исключение при обращении к несуществующему свойству класса.
 * Вызывается отсутствующее свойство класса с перегруженными свойствами.
 * Версия с бэктрейсом.
 */
final class Lib_Exception_UnknownProperty_Backtraced extends Lib_Exception_UnknownProperty implements Lib_Exception_Backtrace_Interface
{
}
