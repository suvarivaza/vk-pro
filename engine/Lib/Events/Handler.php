<?php

abstract class Lib_Events_Handler
{
    final public function Run($params)
    {
        return $this->Action($params);
    }

    abstract protected function Action($params);
}
