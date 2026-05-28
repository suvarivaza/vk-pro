<?php

namespace Service\System;

/**
 * Class Model_Factory
 *
 * @package Service\Users
 *
 * @property Model_Settings $settings
 */
class Model_Factory
{
    private $_settings = null;

    public function __get($name)
    {
        switch ($name) {
            case 'settings':
                if ($this->_settings === null) {
                    $this->_settings = new Model_Settings($this);
                }

                return $this->_settings;
        }
        throw new \Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }
}
