<?php

/**
 * Class Lib_ORM_Cql.
 */
class Lib_ORM_Cql
{
    /**
     * @var Lib_ORM_SphinxQL
     */
    protected $_sphinx;

    public function __construct()
    {
        $this->_sphinx = new Lib_ORM_SphinxQL();
    }

    /**
     * @param string $keyspace
     *
     * @return \System\Cassa\Client
     */
    public function getPool($keyspace)
    {
        return new \System\Cassa\Client($keyspace);
    }

    /**
     * @param Lib_ORM_CqlObject $object
     * @param string $keyspace
     * @param string $table
     *
     * @return bool
     */
    protected function _save(Lib_ORM_CqlObject $object, $keyspace, $table)
    {
        $cassandra = $this->getPool($keyspace);
        $update = $cassandra->update()->to($table);

        foreach ($object as $col => $spec) {
            $const = $object::getCqlConst($object->$col, $spec);

            if ($spec & $object::FLAG_PK) {
                $update->eq($col, $const);
            } else {
                $update->set($col, $const);
            }
        }

        $cassandra->query($update);

        $this->_sphinx->save(
            $object,
            $keyspace,
            $table
        );

        return true;
    }

    /**
     * @param Lib_ORM_CqlObject $object
     * @param string $keyspace
     * @param string $table
     *
     * @return Lib_ORM_CqlObject|null
     *
     * @throws Lib_Exception_Logic
     */
    protected function _getById(Lib_ORM_CqlObject $object, $keyspace, $table)
    {
        foreach ($object::getPkSpec() as $field => $type) {
            if (!isset($object->$field)) {
                throw new \Lib_Exception_Logic(
                    'All primary key fields must be set.'
                );
            }
        }

        $result = $this->_getPrimaryCollection($object, $keyspace, $table, 1);

        if (empty($result)) {
            return null;
        }

        return $result[0];
    }

    /**
     * @param Lib_ORM_CqlObject $object
     * @param string $keyspace
     * @param string $table
     * @param int $limit
     *
     * @return Lib_ORM_CqlObject[]
     *
     * @throws Lib_Exception_Logic
     */
    protected function _getPrimaryCollection(Lib_ORM_CqlObject $object, $keyspace, $table, $limit = 1000)
    {
        $cassandra = $this->getPool($keyspace);
        $select = $cassandra->select()->all()->from($table)->limit($limit);
        $continuous = true;

        foreach ($object::getPkSpec() as $field => $type) {
            if (!isset($object->$field)) {
                $continuous = false;
            } else {
                if (!$continuous) {
                    throw new \Lib_Exception_Logic(
                        'You must specify continuous list of primary keys.'
                    );
                }

                $select->eq($field, $object::getCqlConst($object->$field, $type));
            }
        }

        $result = [];

        foreach ($cassandra->query($select) as $row) {
            $tmp = clone $object;

            foreach ($row as $k => $v) {
                $tmp->$k = $v;
            }

            $result[] = $tmp;
        }

        return $result;
    }

    /**
     * @param Lib_ORM_CqlObject $object
     * @param string $keyspace
     * @param string $table
     *
     * @return bool
     */
    protected function _deleteById(Lib_ORM_CqlObject $object, $keyspace, $table)
    {
        $cassandra = $this->getPool($keyspace);
        $delete = $cassandra->delete()->from($table);

        foreach ($object::getPkSpec() as $field => $type) {
            $delete->eq($field, $object::getCqlConst($object->$field, $type));
        }

        $cassandra->query($delete);

        $this->_sphinx->delete(
            $object,
            $keyspace,
            $table
        );

        return true;
    }
}
