<?php

namespace System;

/**
 * Controller with routing capabilities.
 */
interface Service_Controller_Router_Interface extends Service_Controller_Interface
{
    /**
     * @param string $path
     *
     * @return bool|string
     */
    public function Route($path);

    /**
     * @return string
     */
    public function getExecutedState();
}
