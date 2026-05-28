<?php

/**
 * Class Lib_ORM_SphinxIterator.
 */
class Lib_ORM_SphinxIterator extends Lib_ORM_Iterator
{
    /**
     * @param Lib_DB_Adapter $db
     *
     * @return int
     */
    protected function _getTotal(\Lib_DB_Adapter $db)
    {
        $result = $db->query('SHOW META')->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($result as $row) {
            if ($row['Variable_name'] == 'total_found') {
                return (int) $row['Value'];
            }
        }

        return 0;
    }
}
