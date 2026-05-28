<?php

class Lib_Exception extends Exception
{
    protected $user_error_args = [];

    /**
     * @param string $message
     * @param int $code
     * @param array $user_error_args
     */
    public function __construct($message = '', $code = 0, $user_error_args = [])
    {
        parent::__construct($message, $code);

        if ($this instanceof Lib_Exception_Backtrace_Interface) {
            Lib_Trace::BacktraceException($this);
        }

        if (false === is_array($user_error_args)) {
            $user_error_args = [];
        }

        $this->user_error_args = $user_error_args;
    }

    /**
     * Возвращает массив с данными для кода ошибки.
     *
     * @return array
     */
    public function getUserErrorArgs()
    {
        return $this->user_error_args;
    }

    public function __toString()
    {
        $message = sprintf(
            'Exception %s: "%s", with code %d in file "%s:%d"',
            get_class($this),
            $this->getMessage(),
            $this->getCode(),
            $this->getFile(),
            $this->getLine()
        );

        return $message;
    }
}
