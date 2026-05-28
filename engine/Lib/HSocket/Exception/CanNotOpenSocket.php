<?php

class Lib_HSocket_Exception_CanNotOpenSocket extends Lib_Exception_Runtime implements Lib_Exception_Backtrace_Interface
{
    public function __construct($host, $port, $errno, $errmsg)
    {
        $msg = 'Can not open socket to ' . $host . ':' . $port;

        if ($errno || $errmsg) {
            $msg .= ',' . ($errno ? ' error ' . $errno : '') . ($errmsg ? ' ' . $errmsg : '');
        }
        parent::__construct($msg);
    }
}
