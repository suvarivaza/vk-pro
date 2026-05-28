<?php

function logs($body, $file_name)
{
//    if (is_array($body) or is_object($body)) {
//        $body = json_encode($body, JSON_UNESCAPED_UNICODE);
//    }
    $fd = fopen(VAR_PATH . "/logs/{$file_name}", 'a') or die("не удалось создать файл");
    fwrite($fd, print_r($body, 1));
    fclose($fd);
}

function clearLogFile($file_name)
{
    file_put_contents(VAR_PATH . "/logs/{$file_name}", '');
}

function log_info($step, $message = '-', $name = 'log')
{
    $date = new DateTime();
    $date = $date->format("d.m.Y H:i:s");
    $line = $date . ' | ' . trim($step) . ' | ' . trim($message);
    @file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $name . '.txt', $line . "\r\n", FILE_APPEND);
}

function redirect($location)
{
    return header("Location: {$location}");
}

function dd($data, $isDie = true)
{
    if ($GLOBALS['isSuperUser'] or !empty($_GET['dd'])) {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
        if($isDie) die;
    }
}

function vd($data, $isDie = true)
{
    if ($GLOBALS['isSuperUser']) {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
        if($isDie) die;
    }
}

function pd($data, $isDie = true)
{
    if ($GLOBALS['isSuperUser']) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        if ($isDie) die;
    }
}

function logMail($subject, $massage)
{
    return mail('42-36-42@mail.ru', $subject, $massage);
}

function getTrace($withArgs = false)
{
    if ($GLOBALS['isSuperUser']) {
        echo '<pre>';
        if ($withArgs === false) print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
        else print_r(debug_backtrace());
    }
}

function clearFile($filePath, $countRows)
{

    $file = file($filePath); //читаем файл и пишем его в массив
    $fp = fopen($filePath, "w"); //открываем файл для записи
    flock($fp, LOCK_EX); //блокируем файл

    //очищаем до указанного в $countRows количества строк
    $i = 0;
    do {
        unset($file[$i]);
        $i++;
    } while (count($file) > $countRows);

    fwrite($fp, implode("", $file)); //пишем в файл
    flock($fp, LOCK_UN); //разблокируем файл
    fclose($fp);
    //@chmod("$filePath", 0644);
}

function clearFileOOP()
{
    $file = new SplFileObject($_SERVER['DOCUMENT_ROOT'] . $file_path);

    $file->seek(PHP_INT_MAX);
    $total_lines = $file->key();
    $file->seek($total_lines - 50); //указываем скольк берем строк файла
    $result = [];
    while (!$file->eof()) {
        $result[] = $file->current();
        $file->next();
    }

    $file = '';
    $newFile = '.' . $file;
    $oldFile =

    rename($file, 'tmp_' . $file);

    $countRows = 500;
    $new_file = new SplFileObject($file, 'w'); //открываем новый файл для записи

    //читаем старый файл нужное количество строк и пишем их в новый файл
    foreach (new LimitIterator(new SplFileObject('tmp_' . $file), $countRows) as $line)
        $new_file->fwrite($line);
}