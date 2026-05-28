<?php

namespace Suvarivaza;

use DateTime;


class ErrorHandler
{

    private $logsPath = '/logs/';


    private $errors = [
        E_ERROR => 'ERROR', //фатальные (выбрасывается исключение)
        E_WARNING => 'WARNING',
        E_PARSE => 'PARSE', //фатальные
        E_NOTICE => 'NOTICE',
        E_CORE_ERROR => 'CORE_ERROR', //фатальные
        E_CORE_WARNING => 'CORE_WARNING',
        E_COMPILE_ERROR => 'COMPILE_ERROR', //фатальные
        E_COMPILE_WARNING => 'COMPILE_WARNING',
        E_USER_ERROR => 'USER_ERROR',
        E_USER_WARNING => 'USER_WARNING',
        E_USER_NOTICE => 'USER_NOTICE',
        E_STRICT => 'STRICT',
        E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR', //фатальные (выбрасывается исключение)
        E_DEPRECATED => 'DEPRECATED',
        E_USER_DEPRECATED => 'USER_DEPRECATED',
    ];

    /*
     * задаем типы ошибок которые будем перехватывать - https://www.php.net/manual/ru/errorfunc.constants.php
     */
    //private $errorTypes =  E_ALL; //Если нужно логировать все ошибки. Остарожно, очень много ошибок!
    private $errorTypes = E_ERROR | E_WARNING | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR;


    private $fatalErrors = [
        E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_RECOVERABLE_ERROR
    ];


    /**
     * возвращаем, в зависимости от кода, название ошибки
     * @param $error
     * @return string
     */
    public function getErrorCodeName($error)
    {

        if (array_key_exists($error, $this->errors)) {
            return $this->errors[$error] . " [$error]";
        }

        return $error;
    }


    /**
     * Зарегистрируем этот метод в качестве собственных:
     * 1. обработчика ошибок (как обычных так и фатальных)
     * 2. ловца исключений, выброшенных вне блока try{} catch(){}
     */
    public function register()
    {

        //Какие ошибки можно поймать с помощью set_error_handler():
        //Перехватываемые (не фатальные и смешанные)
        //E_USER_ERROR, E_RECOVERABLE_ERROR, E_WARNING, E_NOTICE, E_USER_WARNING, E_USER_NOTICE, E_STRICT, E_DEPRECATED, E_USER_DEPRECATED.
        //Не перехватываемые (фатальные)
        //E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING


        // регистрируем обработчик ошибок
        set_error_handler([$this, 'errorHandler'], $this->errorTypes);

        // регистрируем обработчик не пойманных исключений (выбрашеных вне блока try catch)
        // После вызова exceptionHandler выполнение скрипта будет остановлено.
        // начиная с версии PHP 7.1 ловит так же фатальные ошибки E_ERROR и E_RECOVERABLE_ERROR (выбрасываемые классом Error)
        //если отключен то все фатальные ошибку будет обрабатыать fatalErrorHandler
        //set_exception_handler([$this, 'exceptionHandler']);

        // регистрируем функцию, выполняющуюся перед завершением скрипта
        // нужна для отлова фатальных ошибок.
        register_shutdown_function([$this, 'fatalErrorHandler']);
    }

    /**
     * Метод, который теперь вместо php будет обрабатывать ошибки.
     * Обратите внимание, что метод возвращает true,
     * если он вернет false или null, то обработка ошибок будет передана встроенному обработчику PHP
     *
     * $errno — первый аргумент содержит тип ошибки в виде целого числа
     * $errstr $errstr — второй аргумент содержит сообщение об ошибке
     * $file $errfile — необязательный третий аргумент содержит имя файла, в котором произошла ошибка
     * $line $errline — необязательный четвертый аргумент содержит номер строки, в которой произошла ошибка
     * $errcontext — необязательный пятый аргумент содержит массив всех переменных, существующих в области видимости, где произошла ошибка
     * @return bool
     */
    public function errorHandler($errno, $errstr, $file, $line, $errcontext)
    {

        // Кроме случаев, когда мы подавляем ошибки с помощью @
        if (error_reporting() === 0) {
            return false;
        }

        // выводим информацию об ошибке в браузере для SUPER_USER
        $this->showError('errorHandler', $errno, $errstr, $file, $line);

        $this->logErrorHandler('PHP Error', $errstr, $file, $line, $errno);

        // возвращаем true, чтоб управление обработкой ошибок НЕ было передано встроенному обработчику
        return true;
    }

    /**
     * Метод, который будет обрабатывать все не обработанные исключения, вызванные вне блока try/catch
     * отлавливает так же фатальные ошибки E_ERROR и E_RECOVERABLE_ERROR
     *
     * @param \Throwable $e
     * Throwable это родительский класс - интерфейс для Exception и Error
     * @since version
     */
    public function exceptionHandler(\Throwable $e)
    {

        //выведем выбрашенное исключение для пользователя SUPER_USER
        $this->showError('exceptionHandler', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine(), 404);

        $this->logException($e);
    }

    /**
     * Метод, который фиксирует наличие фатальной ошибки
     * и обрабатывает ее.
     */
    public function fatalErrorHandler()
    {

        $error = error_get_last();

        // если в буфере находим фатальную ошибку
        if (!empty($error) and in_array($error['type'], $this->fatalErrors)) {
            ob_end_clean();// сбросить буфер, завершить работу буфера

            $this->showError('fatalErrorHandler', $error['type'], $error['message'], $error['file'], $error['line'], 500);

            $this->logFatalErrorHandler('FATAL ERROR!', $error['message'], $error['file'], $error['line'], $error['type']);
            logMail('Vk-Pro.top FATAL ERROR!', "fatalErrorHandler: error message: {$error['message']}  file:{$error['file']} line:{$error['line']}  error type: " . $this->getErrorCodeName($error['type']));
        }

        // в противном случае, ничего не делаем, оставляем работу скрипта на усмотрение встроенного обработчика.

    }

    /**
     * Вспомогательный метод,
     * который выводит информацию о случившемся в виде текста в браузере для пользователя с правами SuperUser
     *
     * @param $errno
     * @param $errstr
     * @param $file
     * @param $line
     * @param int $status
     */
    public function showError($type, $errno, $errstr, $file, $line, $status = 500)
    {
        if ($GLOBALS['isSuperUser']) {

            header("HTTP/1.1 $status");
            echo "<b>{$type} | " . $this->getErrorCodeName($errno) . "</b><hr>" . $errstr . '<hr> File: ' . $file . '<hr> Line: ' . $line . '<hr>';
            echo '<br>';
            echo '<pre>';
            debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            echo '<br>';
            //debug_print_backtrace();
            //print_r(debug_backtrace());
        }

    }


    public function logErrorHandler($errorType, $errorMessage, $errorFile, $errorLine, $errorCode = null)
    {
        if ($errorCode) $ErrorCodeName = $this->getErrorCodeName($errorCode) ? ' ErrorCodeName: ' . $this->getErrorCodeName($errorCode) : '';
        $this->log("errorHandler | {$errorType} {$ErrorCodeName} ErrorFile: {$errorFile} ErrorLine: {$errorLine}", " ErrorMessage: {$errorMessage}", 'logErrorHandler');
    }


    public function logFatalErrorHandler($errorType, $errorMessage, $errorFile, $errorLine, $errorCode = null)
    {
        if ($errorCode) $ErrorCodeName = $this->getErrorCodeName($errorCode) ? ' ErrorCodeName: ' . $this->getErrorCodeName($errorCode) : '';
        $this->log("fatalErrorHandler | {$errorType} {$ErrorCodeName} ErrorFile: {$errorFile} ErrorLine: {$errorLine}", " ErrorMessage: {$errorMessage}", 'logFatalErrorHandler');
    }


    /***
     * @param \Throwable $e
     * Логируем выбрашенные исключения
     */
    public function logException(\Throwable $e)
    {

        //форматируем вывод трейсировки ошибки
        $errorMassage = $e->getMessage();
        $line = $e->getLine();
        $file = $e->getFile();
        $trace = explode("\n", $e->getTraceAsString());
        $trace = array_reverse($trace); // reverse array to make steps line up chronologically
        array_shift($trace); // remove {main}
        array_pop($trace); // remove call to this method
        $length = count($trace);
        $result = [];

        for ($i = 0; $i < $length; $i++) {
            $result[] = ($i + 1) . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
        }

        $traceResult = "\t" . implode("\n\t", $result);

        //пишем лог
        $this->log("exceptionHandler | \n" . get_class($e) . " File: {$file} Line: {$line}", "\nErrorMessage: {$errorMassage} \nTrace:\n {$traceResult}", 'logExceptionsHandler');

    }


    function log($step, $message = '-', $fileName = 'log')
    {
        $date = new DateTime();
        $date = $date->format("d.m.Y H:i:s");
        $log_info = $date . ' | ' . trim($step) . ' | ' . trim($message);
        @file_put_contents("{$_SERVER['DOCUMENT_ROOT']}{$this->logsPath}{$fileName}.txt", $log_info . "\r\n", FILE_APPEND | LOCK_EX);

    }


}