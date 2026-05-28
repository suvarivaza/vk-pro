<?php

class Lib_ORM_SQL extends Lib_ORM
{
    /**
     * @param mixed $id
     * @param Lib_ORM_Object $obj
     * @param string $db
     * @param string $table
     * @param string $field
     *
     * @return bool
     *
     * @throws \Lib_Exception_Logic_Backtraced
     */
    protected function _saveDifferencesGetSQL($id, Lib_ORM_Object $obj, $db, $table, $field)
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
            if ($prop == $field) {
                continue;
            }
            $obj->checkProperty($prop);

            switch ($fields[$prop]) {
                case Lib_ORM_Object::TYPE_TIMESTAMP:
                case Lib_ORM_Object::TYPE_DATETIME:
                    $value = call_user_func([$obj, '__get'], $prop);

                    if (null === $value) {
                        $values[] = '`' . $prop . '` = NULL';
                    } else {
                        $values[] = '`' . $prop . "` = '" . \Lib_TimeStamp::createFromTimestamp($value)->format(\Lib_TimeStamp::MYSQL_FORMAT) . "'";
                    }
                    break;
                case Lib_ORM_Object::TYPE_DATE:
                    $value = call_user_func([$obj, '__get'], $prop);

                    if (null === $value) {
                        $values[] = '`' . $prop . '` = NULL';
                    } else {
                        $values[] = '`' . $prop . "` = '" . \Lib_TimeStamp::createFromTimestamp($value)->format('Y-m-d') . "'";
                    }
                    break;
                case Lib_ORM_Object::TYPE_FLOAT:
                    $value = call_user_func([$obj, '__get'], $prop);

                    if (null === $value) {
                        $values[] = '`' . $prop . '` = NULL';
                    } else {
                        $values[] = '`' . $prop . '` = ' . \Lib_DB::NormalizeFloat($value);
                    }
                    break;
                default:
                    $values[] = '`' . $prop . "` = '" . call_user_func([$obj, '__get'], $prop) . "'";
            }
        }

        if (!count($values)) {
            return true;
        }

        $sql = 'UPDATE `' . $table . '` SET';
        $sql .= implode(',', $values);
        $sql .= ' WHERE `' . $field . '` = ' . $id;

        return $sql;
    }

    /**
     * @param Lib_ORM_Object $obj
     * @param string $db
     * @param string $table
     * @param string $field
     *
     * @return int|null
     */
    protected function _insertGetSQL(Lib_ORM_Object $obj, $db, $table, $field)
    {
        $obj->checkProperties();
        $fields = $obj->GetPropertiesTypesNoFlags();
        $values = [];

        foreach ($fields as $prop => $type) {
            if ($prop == $field) {
                continue;
            }

            switch ($type) {
                case Lib_ORM_Object::TYPE_TIMESTAMP:
                case Lib_ORM_Object::TYPE_DATETIME:
                    $value = call_user_func([$obj, '__get'], $prop);

                    if (null === $value) {
                        $values[] = '`' . $prop . '` = NULL';
                    } else {
                        $values[] = '`' . $prop . "` = '" . \Lib_TimeStamp::createFromTimestamp($value)->format(\Lib_TimeStamp::MYSQL_FORMAT) . "'";
                    }
                    break;
                case Lib_ORM_Object::TYPE_DATE:
                    $value = call_user_func([$obj, '__get'], $prop);

                    if (null === $value) {
                        $values[] = '`' . $prop . '` = NULL';
                    } else {
                        $values[] = '`' . $prop . "` = '" . \Lib_TimeStamp::createFromTimestamp($value)->format('Y-m-d') . "'";
                    }
                    break;
                case Lib_ORM_Object::TYPE_FLOAT:
                    $value = call_user_func([$obj, '__get'], $prop);

                    if (null === $value) {
                        $values[] = '`' . $prop . '` = NULL';
                    } else {
                        $values[] = '`' . $prop . '` = ' . \Lib_DB::NormalizeFloat($value);
                    }
                    break;
                default:
                    $values[] = '`' . $prop . "` = '" . call_user_func([$obj, '__get'], $prop) . "'";
            }
        }

        $sql = 'INSERT INTO `' . $table . '` SET';

        $sql .= implode(',', $values);

        return $sql;
    }

    /**
     * @param mixed $id
     * @param string $db
     * @param string $table
     * @param string $field
     *
     * @return bool
     */
    protected function _deleteBySQL($id, $db, $table, $field)
    {
        $sql = 'DELETE FROM `' . $table . '` WHERE `' . $field . "` = '" . $id . "'";
    }
}
