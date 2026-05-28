<?php

/**
 * Реализация автозагрузчика для библиотек.
 *
 * В полном имени класса, включающим в себя имя пространства имен все символы _ и \ заменяются на /. Класс ищется по
 * полученному пути, относительно папки include.
 *
 * Например, класс Lib_Passport будет искалься в include/Lib/Passport.php
 *
 * Поддерживает загрузку легаси-классов-исключений, например: App.
 */

/**
 * Реализация автозагрузчика, который кидает исключение
 * при неуспешной попытке загрузки класса другими автозагрузчиками.
 *
 * Не кидает исключение, если вызван из class_exists().
 *
 * @throws Lib_Exception_Runtime
 */


spl_autoload_register(function ($class) {
    static $map = array(
        'App' => 'System_App',
        'STPL' => 'Lib_Stpl',
        'Stpl' => 'Lib_Stpl',
        'Request' => 'System_HTTP_Request',
        'Response' => 'System_HTTP_Response',
    );

    if (isset($map[$class])) {
        $class = $map[$class];
    }


    $file = ENGINE_PATH . 'engine/' . str_replace(['_', '\\'], DIRECTORY_SEPARATOR, $class) . '.php';

    if (!empty($file) and file_exists($file)) {
        include_once $file;
    }
});

spl_autoload_register(function ($class) {
    if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
    } else {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    }

    if (false === isset($backtrace[2]['function']) || $backtrace[2]['function'] != 'class_exists') {
        throw new \Exception('Class "' . $class . '" can not be loaded.');
    }
});

STPL::Init(array(ENGINE_PATH . '/templates/'));