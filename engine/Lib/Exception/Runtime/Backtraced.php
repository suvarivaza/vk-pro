<?php

/**
 * Критическое исключение в ходе работы приложения.
 * Надо использовать когда при обращении к какой-нить библиотеке.
 * или работе с внешними ресурсами возникла ошибка.
 * Например, не удалось подключиться к БД, не удалось сохранить файл на диск.
 * Версия с бэктрейсом.
 */
final class Lib_Exception_Runtime_Backtraced extends Lib_Exception_Runtime implements Lib_Exception_Backtrace_Interface
{
}
