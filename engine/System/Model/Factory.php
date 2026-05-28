<?php

namespace System;

/**
 * Class Model_Factory
 *
 * @package System
 *
 * @property Model_Proxies $proxies
 */
class Model_Factory
{
    private $_proxies = null;

    public function __get($name)
    {
        switch ($name) {
            case 'proxies':
                if (null === $this->_proxys) {
                    $this->_proxies = new Model_Proxies($this);
                }

                return $this->_proxies;
        }
    }
}
