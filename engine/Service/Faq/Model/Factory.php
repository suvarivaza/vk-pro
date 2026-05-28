<?php

namespace Service\Faq;

use Database_Main;
use Lib_DB_Adapter;
use Lib_DB_Factory;

/**
 * Class Model_Factory
 *
 * @package Service\Messages
 *
 * @property Model_Rubrics $rubrics
 * @property Model_Faq $faq
 * @property Model_Questions $questions
 * @property Lib_DB_Adapter $db
 */
class Model_Factory
{
    private $_rubrics = null;
    private $_faq = null;
    private $_questions = null;
    private $_db = null;

    public function __get($name)
    {
        switch ($name) {
            case 'rubrics':
                if ($this->_rubrics === null) {
                    $this->_rubrics = new Model_Rubrics($this);
                }

                return $this->_rubrics;
            case 'faq':
                if ($this->_faq === null) {
                    $this->_faq = new Model_Faq($this);
                }

                return $this->_faq;
            case 'questions':
                if ($this->_questions === null) {
                    $this->_questions = new Model_Questions($this);
                }

                return $this->_questions;
            case 'db':
                if (null === $this->_db) {
                    $this->_db = Lib_DB_Factory::GetInstance(new Database_Main());
                }

                return $this->_db;
        }
    }
}
