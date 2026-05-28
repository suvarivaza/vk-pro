<?php

namespace Service\Tasks;

/**
 * Class Model_Factory
 *
 * @package Service\Tasks
 *
 * @property Model_Tasks $tasks
 * @property Model_Users $users
 * @property Model_Abuses $abuses
 * @property Model_Specials_Groups $specialGroups
 * @property Model_Specials $specials
 * @property \Lib_DB_Adapter $db
 */
class Model_Factory
{
    private $_tasks = null;
    private $_tasksUsers = null;
    private $_abuses = null;
    private $_specials = null;
    private $_specialGroups = null;
    private $_db = null;

    public function __get($name)
    {
        switch ($name) {
            case 'db':
                if ($this->_db === null) {
                    $this->_db = \Lib_DB_Factory::GetInstance(new \Database_Main());
                }

                return $this->_db;
            case 'tasks':
                if ($this->_tasks === null) {
                    $this->_tasks = new Model_Tasks($this);
                }

                return $this->_tasks;
            case 'users':
                if ($this->_tasksUsers === null) {
                    $this->_tasksUsers = new Model_Users($this);
                }

                return $this->_tasksUsers;
            case 'abuses':
                if ($this->_abuses === null) {
                    $this->_abuses = new Model_Abuses($this);
                }

                return $this->_abuses;
            case 'specialGroups':
                if ($this->_specialGroups === null) {
                    $this->_specialGroups = new Model_Specials_Groups($this);
                }

                return $this->_specialGroups;
            case 'specials':
                if ($this->_specials === null) {
                    $this->_specials = new Model_Specials($this);
                }

                return $this->_specials;
        }
        throw new \Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }

    public function isFromMaster()
    {
        return true;
    }
}
