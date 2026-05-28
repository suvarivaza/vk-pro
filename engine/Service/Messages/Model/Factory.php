<?php

namespace Service\Messages;

/**
 * Class Model_Factory
 *
 * @package Service\Messages
 *
 * @property Model_Messages $messages
 * @property Model_Users $users
 */
class Model_Factory
{
    private $_messages = null;
    private $_users = null;

    public function __get($name)
    {
        switch ($name) {
            case 'messages':
                if ($this->_messages === null) {
                    $this->_messages = new Model_Messages($this);
                }

                return $this->_messages;
            case 'users':
                if ($this->_users === null) {
                    $this->_users = new Model_Users($this);
                }

                return $this->_users;
        }
    }
}
