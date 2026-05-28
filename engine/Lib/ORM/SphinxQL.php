<?php

/**
 * Class Lib_ORM_SphinxQL.
 */
class Lib_ORM_SphinxQL
{
    /**
     * @param Lib_ORM_CqlObject $object
     * @param string $db
     * @param string $table
     *
     * @return bool
     */
    public function save(Lib_ORM_CqlObject $object, $db, $table)
    {
        $props = $object->getPropSpec();

        $cols = ['id' /* format */];
        $values = [$object->getHash()];

        foreach ($props as $prop => $flags) {
            $cols[] = $prop;
            $values[] = $this->_quote($object->$prop, $flags & Lib_ORM_CqlObject::MASK_TYPE);
        }

        $cols = implode(', ', $cols);
        $values = implode(', ', $values);

        \Lib_DB_Factory::GetInstance(null, $db)->query(
            "REPLACE INTO $table ($cols) VALUES ($values)"
        );

        return true;
    }

    /**
     * @param Lib_ORM_CqlObject $object
     * @param string $db
     * @param string $table
     *
     * @return bool
     */
    public function delete(Lib_ORM_CqlObject $object, $db, $table)
    {
        \Lib_DB_Factory::GetInstance(null, $db)->query(
            "DELETE FROM $table WHERE id = " . $object->getHash()
        );

        return true;
    }

    private function _quote($val, $type)
    {
        switch ($type) {
            case Lib_ORM_CqlObject::TYPE_BIGINT:
            case Lib_ORM_CqlObject::TYPE_COUNTER:
            case Lib_ORM_CqlObject::TYPE_INT:
            case Lib_ORM_CqlObject::TYPE_VARINT:
            case Lib_ORM_CqlObject::TYPE_TIMESTAMP:
            case Lib_ORM_CqlObject::TYPE_BLOB:

            case Lib_ORM_CqlObject::TYPE_DECIMAL:
            case Lib_ORM_CqlObject::TYPE_DOUBLE:
            case Lib_ORM_CqlObject::TYPE_FLOAT:
                return (string) $val;

            case Lib_ORM_CqlObject::TYPE_TIMEUUID:
                return (string) (int) \Lib_Uuid::getTimeFromUuid($val);

            case Lib_ORM_CqlObject::TYPE_UUID:
                return $this->_quote(\Lib_Uuid::toString($val), Lib_ORM_CqlObject::TYPE_TEXT);

            case Lib_ORM_CqlObject::TYPE_TEXT:
                return "'" . addcslashes($val, "'") . "'";

            default:
                return $this->_quote($val, Lib_ORM_CqlObject::TYPE_TEXT);
        }
    }
}
