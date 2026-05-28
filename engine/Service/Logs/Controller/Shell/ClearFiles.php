<?php

namespace Service\Logs;

//Класс для очистки лог файлов

use System\Service_Controller_Shell;

class Controller_Shell_ClearFiles extends Service_Controller_Shell
{

    private $path = VAR_PATH . 'logs/crons/'; //путь до каталога с лог файлами
    private $maxFileSize = 10000000; //максимальный размер файла 10мб - файлы больше 10мб будем чистить
    private $beforeFilesSize = 0;
    private $afterFilesSize = 0;
    private $countFiles = 0;

    public function A_Clear()
    {

        //для логов
        $tm = time();
        $date = new \DateTime();
        $date = $date->format("Y-m-d H:i:s");
        echo "\naction=Logs/ClearFiles:Clear ";
        echo $date;

        //перебираем файлы в директории
        $dir = new \DirectoryIterator($this->path);
        foreach ($dir as $fileinfo) {

            $fileSize = $fileinfo->getSize();
            if ($fileSize < $this->maxFileSize) continue;

            $this->beforeFilesSize += $fileSize;

            if ($fileinfo->getExtension() === 'log') { //берем файлы только с расширением log
                $fileName = $fileinfo->getFilename();
                $this->clearFile($fileName, $fileSize);
            }
        }

        echo "\nОчищено файлов: " . $this->countFiles;
        echo "\nОчищено байт: " . ($this->beforeFilesSize - $this->afterFilesSize);
        echo "\nВремя выполнения скрипта: " . round((time() - $tm) / 60, 2);
        echo "\n";

    }


    private function clearFile($fileName, $fileSize)
    {

        //$fileName = 'Bot-Bot.log';
        $file = $this->path . $fileName;
        $tpmFile = $this->path . 'tmp_' . $fileName;

        echo "\nОчищаем файл: {$fileName} Размер: $fileSize";

        //переименуем основной файл во временный
        $res = rename($file, $tpmFile);
        if (!$res) return;

        //читаем временный файл
        $TmpFile = new \SplFileObject($tpmFile);

        //посчитаем сколько всего строк во временном файле
        $TmpFile->seek(PHP_INT_MAX); //ставим курсор на последнюю строку
        $totalLines = $TmpFile->key(); //получаем номер последней строки

        //открываем новый файл для записи (будет создан)
        $NewFile = new \SplFileObject($file, 'w');

        //посчитаем какую часть строк оставить
        $count = (int)($fileSize / $this->maxFileSize) + 1;
        $countLines = (int)($totalLines / $count); //количество последних строк которое оставляем
        $offset = $totalLines - $countLines;

        //перезаписываем новый файл с нужным количеством строк
        foreach (new \LimitIterator($TmpFile, $offset) as $line){
            $NewFile->fwrite($line);
        }

        $newFileSize = filesize($file);
        echo " Сократили в {$count} раз! Новый размер: {$newFileSize}";
        $this->afterFilesSize += $newFileSize;
        $this->countFiles++;

        //удаляем временный файл
        unlink($tpmFile);

    }

}