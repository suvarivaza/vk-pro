<?php

class Lib_HSocket_Exception_SocketGoneAway extends Lib_Exception_Runtime implements Lib_Exception_Backtrace_Interface
{
    /**
     * @param string $host
     * @param int $db_name
     * @param int $errno
     * @param int $last_connected
     */
    public function __construct($host, $db_name, $errno, $last_connected)
    {
        $msg = 'Socket on ' . $host . '/' . $db_name . ' has gone away with error code '
            . $errno . '. '
            . ($last_connected !== null ? (microtime(true) - $last_connected) . 's since last connection and ' : '')
            . (microtime(true) - $GLOBALS['LOG_PROGRAM_START']) . ' since program start. Try to reconnect. ';

        parent::__construct($msg, Lib_HSocket_Socket::ERR_CONNECTION_RESET_BY_PEER);
    }
}
