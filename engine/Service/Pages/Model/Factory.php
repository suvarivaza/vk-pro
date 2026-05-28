<?php

namespace Service\Pages;

/**
 * Class Model_Factory
 *
 * @package Service\Pages
 *
 * @property Model_Pages $pages
 * @property Model_Prices $prices
 * @property Model_Prices_Fields $fields
 * @property Model_Prices_Positions $positions
 */
class Model_Factory
{
    private $_pages = null;

    private $_prices = null;

    private $_fields = null;

    private $_positions = null;

    public function __get($name)
    {
        switch ($name) {
            case 'pages':
                if ($this->_pages === null) {
                    $this->_pages = new Model_Pages($this);
                }

                return $this->_pages;

            case 'prices':
                if ($this->_prices === null) {
                    $this->_prices = new Model_Prices($this);
                }

                return $this->_prices;

            case 'fields':
                if ($this->_fields === null) {
                    $this->_fields = new Model_Prices_Fields($this);
                }

                return $this->_fields;

            case 'positions':
                if ($this->_positions === null) {
                    $this->_positions = new Model_Prices_Positions($this);
                }

                return $this->_positions;
        }

        return null;
    }
}
