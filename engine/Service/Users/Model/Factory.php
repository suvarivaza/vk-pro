<?php

namespace Service\Users;

/**
 * Class Model_Factory
 *
 * @package Service\Users
 *
 * @property Model_Users $users
 * @property Model_Baskets $baskets
 * @property Model_Cities $cities
 * @property Model_Countries $countries
 * @property Model_Users_Online $online
 * @property Model_Emails $emails
 * @property Model_Notifications $notifications
 * @property \Lib_DB_Adapter $db
 */
class Model_Factory
{
    private $_users = null;
    private $_online = null;

    private $_baskets = null;

    private $_cities = null;
    private $_countries = null;
    private $_emails = null;
    private $_notifications = null;
    private $_db = null;

    public function __get($name)
    {
        switch ($name) {
            case 'online':
                if ($this->_online === null) {
                    $this->_online = new Model_Users_Online($this);
                }

                return $this->_online;
            case 'users':
                if ($this->_users === null) {
                    $this->_users = new Model_Users($this);
                }

                return $this->_users;
            case 'baskets':
                if ($this->_baskets === null) {
                    $this->_baskets = new Model_Baskets($this);
                }

                return $this->_baskets;
            case 'cities':
                if ($this->_cities === null) {
                    $this->_cities = new Model_Cities($this);
                }

                return $this->_cities;
            case 'countries':
                if ($this->_countries === null) {
                    $this->_countries = new Model_Countries($this);
                }

                return $this->_countries;
            case 'emails':
                if ($this->_emails === null) {
                    $this->_emails = new Model_Emails($this);
                }

                return $this->_emails;
            case 'notifications':
                if ($this->_notifications === null) {
                    $this->_notifications = new Model_Notifications($this);
                }

                return $this->_notifications;
            case 'db':
                if (null === $this->_db) {
                    $this->_db = \Lib_DB_Factory::GetInstance(new \Database_Main());
                }

                return $this->_db;
        }
        throw new \Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }

    public function isFromMaster()
    {
        return true;
    }
}
