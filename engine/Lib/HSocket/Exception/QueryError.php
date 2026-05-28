<?php

class Lib_HSocket_Exception_QueryError extends Lib_Exception_Runtime implements Lib_Exception_Backtrace_Interface
{
    /** @var string */
    private $hs_error;

    public function __construct($queryString, $result, $errno = null)
    {
        $this->hs_error = (string) $result;
        parent::__construct('Error' . ($errno !== null ? ' (type ' . $errno . ')' : '') . ' at executing query "' . $queryString . '" with error "' . $result . '"');
    }

    /**
     * @return string
     */
    public function getHsError()
    {
        return $this->hs_error;
    }
}
