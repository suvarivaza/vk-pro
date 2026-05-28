<?php

namespace Service\News;

use Lib_Exception_UnknownProperty_Backtraced;

/**
 * Class Model_Factory
 *
 * @package Service\Users
 *
 * @property Model_News $news
 */
class Model_Factory
{
    private $_news = null;

    public function __get($name)
    {
        switch ($name) {
            case 'news':
                if ($this->_news === null) {
                    $this->_news = new Model_News($this);
                }

                return $this->_news;
        }
        throw new Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }

    public function isFromMaster()
    {
        return true;
    }
}
