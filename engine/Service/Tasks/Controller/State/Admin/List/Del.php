<?php

namespace Service\Tasks;

class Controller_State_Admin_List_Del extends Controller_State_Admin_Default
{
    protected $_url = '/admin/tasks/list/del?p=@p@';
    protected $_title = 'Удаленные задания';

    protected function _filter(\Lib_ORM_Query $query)
    {
        parent::_filter($query);
        $query->filter->fieldValue('countRemain', '>', 0);
        $query->filter->fieldValue('isDel', '=', true);
    }
}
