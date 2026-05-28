<?php

namespace System;

class Service_Controller_Exception_MissingController extends Service_Controller_Exception
{
    public function __construct($controller)
    {
        parent::__construct(
            "Missing controller '$controller'."
        );
    }
}
