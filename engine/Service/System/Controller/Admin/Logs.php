<?php

namespace Service\System;

class Controller_Admin_Logs extends Controller_Admin
{

    public function actionGet()
    {

        $path = VAR_PATH . 'logs/crons/'; //путь до каталога с лог файлами

        $uri = $this->_request->rawServer['DOCUMENT_URI'];
        if($uri === '/admin/system/logs/grabber-run')
            $fileName = 'Grabber-Run.log';
        else if($uri === '/admin/system/logs/grabber-grab')
            $fileName = 'Grabber-Grab.log';
        else if($uri === '/admin/system/logs/bot-bot')
            $fileName = 'Bot-Bot.log';
        else if($uri === '/admin/system/logs/users-email-send')
            $fileName = 'Users-Email-Send.log';
        else if($uri === '/admin/system/logs/auto-groups-run')
            $fileName = 'Auto-Groups-Run.log';
        else if($uri === '/admin/system/logs/posting-run')
            $fileName = 'Posting-Run.log';
        else if($uri === '/admin/system/logs/tasks-check-5min')
            $fileName = 'Tasks-Check-check5Min.log';
        else if($uri === '/admin/system/logs/tasks-check-hour')
            $fileName = 'Tasks-Check-checkHour.log';
        else if($uri === '/admin/system/logs/tasks-check-day')
            $fileName = 'Tasks-Check-checkDay.log';
        else if($uri === '/admin/system/logs/tasks-check-month')
            $fileName = 'Tasks-Check-checkMonth.log';
        else if($uri === '/admin/system/logs/users-cities')
            $fileName = 'Users-Cities-Run.log';
        else if($uri === '/admin/system/logs/users-bonus-day')
            $fileName = 'Users-Bonus-Day.log';


        $rows = [];
        if(file_exists($path . $fileName)){
            $file = new \SplFileObject($path . $fileName);
            $file->seek(PHP_INT_MAX); //ставим курсор на последнюю строку
            $total_lines = $file->key(); //получаем номер последней строки
            $countRows = $total_lines > 1000 ? 1000 : $total_lines; //сколько забираем строк
            $file->seek($total_lines - $countRows); //смещаем курсор (забираем последние строки)
            while (!$file->eof()) {
                $rows[] = $file->current();
                $file->next();
            }
        }

        $vars = [
            'fileName' => $fileName,
            'rows' => $rows,
        ];
        return $this->_response->setBody(\STPL::Fetch('/admin/logs', $vars));
    }

    /**
     * @return \System\HttpResponse|null
     */
    public function actionPost()
    {
        return null;
    }

}