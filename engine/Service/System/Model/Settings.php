<?php

namespace Service\System;

/**
 * @property Model_Factory $factory
 */
class Model_Settings extends \Lib_ORM
{
    /** @var Model_Factory */
    protected $_factory;

    /** @param Model_Factory */
    public function __construct(Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    public function getNewItem()
    {
        $item = new Model_Settings_Setting($this);

        return $item;
    }

    /**
     * @param $name
     * @param bool $for_save
     *
     * @return Model_Settings_Setting|null
     */
    public function getByName($name, $for_save = false)
    {
        $query = $this->query();
        $query->filter->fieldValue('name', '=', $name);

        if ($for_save) {
            $it = $query->iteratorForSave();
        } else {
            $it = $query->iterator();
        }

        $item = $it->current();

        if (!$item) {
            return null;
        }

        return $item;
    }

    /**
     * @return \Lib_ORM_Query
     */
    public function query()
    {
        $query = new \Lib_ORM_Query(new Model_Settings_Setting($this), new \Database_Main(),
            Model_Settings_Setting::TABLE);

        return $query;
    }

    /**
     * @param bool $for_save
     *
     * @return Model_Settings_Setting[]
     */
    public function getAll($for_save = false)
    {
        $query = $this->query()->limit(1000);

        if ($for_save) {
            $it = $query->iteratorForSave();
        } else {
            $it = $query->iterator();
        }

        $list = [];

        foreach ($it as $item) {
            $list[] = $item;
        }

        return $list;
    }

    public function __get($name)
    {
        switch ($name) {
            case 'factory':
                return $this->_factory;
        }

        throw new \Lib_Exception_UnknownProperty_Backtraced($name, get_class($this));
    }

    /**
     * @param Model_Settings_Setting $setting
     *
     * @return bool|int|null
     */
    public function save(Model_Settings_Setting $setting)
    {
        if ($setting->settingId) {
            $result = parent::_saveDifferencesByIndex($setting->settingId, $setting, new \Database_Main(),
                Model_Settings_Setting::TABLE, 'PRIMARY');
        } else {
            $result = parent::_insert($setting, new \Database_Main(), Model_Settings_Setting::TABLE);
            $setting->settingId = $result;
        }

        return $result;
    }

    public function delete(Model_Settings_Setting $setting)
    {
        return parent::_deleteByIndex($setting->settingId, new \Database_Main(), Model_Settings_Setting::TABLE,
            'PRIMARY');
    }
}
