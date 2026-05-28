<?php

namespace Service\Posting;

/**
 * Class Model_Factory
 *
 * @package Service\Posting
 *
 * @property Model_Groups $groups
 * @property Model_Posts $posts
 * @property Model_Postings $postings
 * @property \Lib_DB_Adapter $db
 */
class Model_Factory
{
    protected $_groups = null;
    protected $_posts = null;
    protected $_postings = null;
    protected $_db = null;

    public function __get($name)
    {
        switch ($name) {
            case 'postings':
                if ($this->_postings === null) {
                    $this->_postings = new Model_Postings($this);
                }

                return $this->_postings;
            case 'groups':
                if ($this->_groups === null) {
                    $this->_groups = new Model_Groups($this);
                }

                return $this->_groups;
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
