<?php

class Lib_ORM
{
    /**
     * @param mixed $id
     * @param Lib_ORM_Object $obj
     * @param string $db
     * @param string $table
     * @param string $index
     *
     * @return bool
     *
     * @throws \Lib_Exception_Logic_Backtraced
     */
    protected function _saveDifferencesByIndex($id, Lib_ORM_Object $obj, $db, $table, $index = 'PRIMARY')
    {

        $fields = $obj->GetPropertiesTypesNoFlags();
        $diff = $obj->getShadowDifference();

        if (null === $diff || !is_array($diff)) {
            throw new \Lib_Exception_Logic_Backtraced('Can\'t get difference from object [' . get_class($obj) . ']. Be sure that factory->getForSave or obj->makeShadow was called.');
        }

        if (!count($diff)) {
            return true;
        }

        $values = [];

        foreach ($diff as $prop) {
            $obj->checkProperty($prop);

            switch ($fields[$prop]) {
                case Lib_ORM_Object::TYPE_TIMESTAMP:
                case Lib_ORM_Object::TYPE_DATETIME:
                    $value = call_user_func([$obj, '__get'], $prop);

                    if (null === $value) {
                        $values[] = null;
                    } else {
                        $values[] = date('Y-m-d H:i:s', $value);
                    }

                    break;
                case Lib_ORM_Object::TYPE_DATE:
                    $value = call_user_func([$obj, '__get'], $prop);

                    if (null === $value) {
                        $values[] = null;
                    } else {
                        $values[] = \Lib_TimeStamp::createFromTimestamp($value)->format('Y-m-d');
                    }
                    break;
                case Lib_ORM_Object::TYPE_FLOAT:
                    $value = call_user_func([$obj, '__get'], $prop);

                    if (null === $value) {
                        $values[] = null;
                    } else {
                        $values[] = \Lib_DB::NormalizeFloat($value);
                    }
                    break;
                default:
                    $values[] = call_user_func([$obj, '__get'], $prop);
            }
        }

        $hs = \Lib_HSocket_Factory::GetInstance($db);
        $idx = $hs->openIndex($table, $index, $diff);

        $updated = $idx->update(\Lib_HSocket_Index::OP_EQUALS, $id, $values);

        return $updated > 0;
    }

    /**
     * @param Lib_ORM_Object $obj
     * @param string $db
     * @param string $table
     * @param string $index
     *
     * @return int|null
     */
    protected function _insert(Lib_ORM_Object $obj, \Database $db, $table, $index = 'PRIMARY')
    {
        $obj->checkProperties();
        $fields = $obj->GetPropertiesTypesNoFlags();
        $values = [];

        foreach ($fields as $prop => $type) {
            switch ($type) {
                case Lib_ORM_Object::TYPE_TIMESTAMP:
                case Lib_ORM_Object::TYPE_DATETIME:
                    $value = call_user_func([$obj, '__get'], $prop);

                    if (null === $value) {
                        $values[] = null;
                    } else {
                        $values[] = \Lib_TimeStamp::createFromTimestamp($value)->format(\Lib_TimeStamp::MYSQL_FORMAT);
                    }
                    break;
                case Lib_ORM_Object::TYPE_DATE:
                    $value = call_user_func([$obj, '__get'], $prop);

                    if (null === $value) {
                        $values[] = null;
                    } else {
                        $values[] = \Lib_TimeStamp::createFromTimestamp($value)->format('Y-m-d');
                    }
                    break;
                case Lib_ORM_Object::TYPE_FLOAT:
                    $value = call_user_func([$obj, '__get'], $prop);

                    if (null === $value) {
                        $values[] = null;
                    } else {
                        $values[] = \Lib_DB::NormalizeFloat($value);
                    }
                    break;
                default:
                    $values[] = call_user_func([$obj, '__get'], $prop);
            }
        }
        $hs = \Lib_HSocket_Factory::GetInstance($db);
        $idx = $hs->openIndex($table, $index, array_keys($fields));
        $id = $idx->insert($values);

        return null !== $id ? (int) $id : $id;
    }

    /**
     * @param mixed $id
     * @param Lib_ORM_Object $obj
     * @param string $db
     * @param string $table
     * @param string $index
     * @param bool $for_save
     *
     * @throws \Lib_Exception_Logic_Backtraced
     *
     * @return bool
     */
    protected function _getOneByIndex($id, Lib_ORM_Object $obj, $db, $table, $index = 'PRIMARY', $for_save = false)
    {
        $fields = $obj->GetPropertiesTypesNoFlags();

        $hs = \Lib_HSocket_Factory::GetInstance($db);
        $idx = $hs->openIndex($table, $index, array_keys($fields));
        $row = $idx->selectRow(\Lib_HSocket_Index::OP_EQUALS, $id);

        if (!is_array($row)) {
            return false;
        }

        foreach ($fields as $name => $type) {
            if (!array_key_exists($name, $row)) {
                throw new \Lib_Exception_Logic_Backtraced('Field ' . $name . ' is not present in row. For ' . get_class($obj));
            }

            if (($type == Lib_ORM_Object::TYPE_DATETIME || $type == Lib_ORM_Object::TYPE_TIMESTAMP) && null !== $row[$name]) {
                call_user_func([$obj, '__set'], $name, \Lib_TimeStamp::createFromFormat(\Lib_TimeStamp::MYSQL_FORMAT, $row[$name])->getTimestamp());
            } elseif ($type == Lib_ORM_Object::TYPE_DATE && null !== $row[$name]) {
                call_user_func([$obj, '__set'], $name, \Lib_TimeStamp::createFromFormat('Y-m-d', $row[$name])->getTimestamp());
            } elseif ($type == Lib_ORM_Object::TYPE_INT && null !== $row[$name]) {
                call_user_func([$obj, '__set'], $name, (int) $row[$name]);
            } elseif ($type == Lib_ORM_Object::TYPE_BOOL && null !== $row[$name]) {
                call_user_func([$obj, '__set'], $name, (bool) $row[$name]);
            } elseif ($type == Lib_ORM_Object::TYPE_FLOAT && null !== $row[$name]) {
                call_user_func([$obj, '__set'], $name, (float) $row[$name]);
            } else {
                call_user_func([$obj, '__set'], $name, $row[$name]);
            }
        }

        if ($for_save) {
            $obj->makeShadow();
        }

        return true;
    }

    /**
     * @param mixed $id
     * @param Lib_ORM_Object $obj
     * @param string $db
     * @param string $table
     * @param string $index
     * @param bool $for_save
     * @param int $limit
     *
     * @throws Lib_Exception_Logic_Backtraced
     *
     * @return Lib_ORM_Object[]
     */
    protected function _getCollectionByIndex($id, Lib_ORM_Object $obj, $db, $table, $index, $for_save = false, $limit = 100000)
    {
        $params = [];
        $fields = $obj->GetPropertiesTypesNoFlags();

        $hs = \Lib_HSocket_Factory::GetInstance($db);
        $idx = $hs->openIndex($table, $index, array_keys($fields));
        $data = $idx->select(\Lib_HSocket_Index::OP_EQUALS, $id, $limit);

        if (count($data)) {
            foreach ($fields as $name => $type) {
                if (!array_key_exists($name, $data[0])) {
                    throw new \Lib_Exception_Logic_Backtraced('Field ' . $name . ' is not present in row. For ' . get_class($obj));
                }
            }
        }

        foreach ($data as $row) {
            $item = clone $obj;

            foreach ($fields as $name => $type) {
                if (($type == Lib_ORM_Object::TYPE_DATETIME || $type == Lib_ORM_Object::TYPE_TIMESTAMP) && null !== $row[$name]) {
                    call_user_func([$item, '__set'], $name, \Lib_TimeStamp::createFromFormat(\Lib_TimeStamp::MYSQL_FORMAT, $row[$name])->getTimestamp());
                } elseif ($type == Lib_ORM_Object::TYPE_DATE && null !== $row[$name]) {
                    call_user_func([$item, '__set'], $name, \Lib_TimeStamp::createFromFormat('Y-m-d', $row[$name])->getTimestamp());
                } elseif ($type == Lib_ORM_Object::TYPE_INT && null !== $row[$name]) {
                    call_user_func([$item, '__set'], $name, (int) $row[$name]);
                } elseif ($type == Lib_ORM_Object::TYPE_BOOL && null !== $row[$name]) {
                    call_user_func([$item, '__set'], $name, (bool) $row[$name]);
                } elseif ($type == Lib_ORM_Object::TYPE_FLOAT && null !== $row[$name]) {
                    call_user_func([$item, '__set'], $name, (float) $row[$name]);
                } else {
                    call_user_func([$item, '__set'], $name, $row[$name]);
                }
            }

            if ($for_save) {
                $item->makeShadow();
            }

            $params[] = $item;
        }

        return $params;
    }

    protected function _getCollectionAllByIndex(Lib_ORM_Object $obj, $db, $table, $index, $for_save = false, $limit = 1000000)
    {
        $params = [];
        $fields = $obj->GetPropertiesTypesNoFlags();

        $hs = \Lib_HSocket_Factory::GetInstance($db);
        $idx = $hs->openIndex($table, $index, array_keys($fields));
        $data = $idx->select(\Lib_HSocket_Index::OP_EQUALS_GREATER, 0, $limit);

        if (count($data)) {
            foreach ($fields as $name => $type) {
                if (!array_key_exists($name, $data[0])) {
                    throw new \Lib_Exception_Logic_Backtraced('Field ' . $name . ' is not present in row. For ' . get_class($obj));
                }
            }
        }

        foreach ($data as $row) {
            $item = clone $obj;

            foreach ($fields as $name => $type) {
                if (($type == Lib_ORM_Object::TYPE_DATETIME || $type == Lib_ORM_Object::TYPE_TIMESTAMP) && null !== $row[$name]) {
                    call_user_func([$item, '__set'], $name, \Lib_TimeStamp::createFromFormat(\Lib_TimeStamp::MYSQL_FORMAT, $row[$name])->getTimestamp());
                } elseif ($type == Lib_ORM_Object::TYPE_DATE && null !== $row[$name]) {
                    call_user_func([$item, '__set'], $name, \Lib_TimeStamp::createFromFormat('Y-m-d', $row[$name])->getTimestamp());
                } elseif ($type == Lib_ORM_Object::TYPE_INT && null !== $row[$name]) {
                    call_user_func([$item, '__set'], $name, (int) $row[$name]);
                } elseif ($type == Lib_ORM_Object::TYPE_BOOL && null !== $row[$name]) {
                    call_user_func([$item, '__set'], $name, (bool) $row[$name]);
                } elseif ($type == Lib_ORM_Object::TYPE_FLOAT && null !== $row[$name]) {
                    call_user_func([$item, '__set'], $name, (float) $row[$name]);
                } else {
                    call_user_func([$item, '__set'], $name, $row[$name]);
                }
            }

            if ($for_save) {
                $item->makeShadow();
            }

            $params[] = $item;
        }

        return $params;
    }

    /**
     * @param mixed $id
     * @param string $db
     * @param string $table
     * @param string $index
     *
     * @return bool
     */
    protected function _deleteByIndex($id, $db, $table, $index = 'PRIMARY')
    {
        $hs = \Lib_HSocket_Factory::GetInstance($db);
        $idx = $hs->openIndex($table, $index, []);
        $deleted = $idx->delete(\Lib_HSocket_Index::OP_EQUALS, $id, PHP_INT_MAX);

        return $deleted > 0;
    }
}
