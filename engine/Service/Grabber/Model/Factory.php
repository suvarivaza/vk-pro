<?php

namespace Service\Grabber;

/**
 * Class Model_Factory
 *
 * @package Service\Grabber
 *
 * @property Model_Groups $groups
 * @property Model_Grabbers $grabbers
 * @property Model_Sources $sources
 * @property Model_Posts $posts
 * @property \Lib_DB_Adapter $db
 */
class Model_Factory
{
    protected $_groups = null;
    protected $_grabbers = null;
    protected $_sources = null;
    protected $_posts = null;
    private $_db = null;

    public function __get($name)
    {
        switch ($name) {
            case 'grabbers':
                if ($this->_grabbers === null) {
                    $this->_grabbers = new Model_Grabbers($this);
                }

                return $this->_grabbers;

            case 'groups':
                if ($this->_groups === null) {
                    $this->_groups = new Model_Groups($this);
                }

                return $this->_groups;
            case 'sources':
                if ($this->_sources === null) {
                    $this->_sources = new Model_Sources($this);
                }

                return $this->_sources;
            case 'posts':
                if ($this->_posts === null) {
                    $this->_posts = new Model_Posts($this);
                }

                return $this->_posts;
            case 'db':
                if (null === $this->_db) {
                    $this->_db = \Lib_DB_Factory::GetInstance(new \Database_Main());
                }

                return $this->_db;
        }
        throw new \Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }
}
