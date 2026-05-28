<?php

namespace Service\Tasks;

class Controller_State_Admin_List_Active extends Controller_State_Admin_Default
{
    protected $_url = '/admin/tasks/list/active?p=@p@';
    protected $_title = 'Активные задания';

    protected function _filter(\Lib_ORM_Query $query)
    {
        parent::_filter($query);
        $query->filter->fieldValue('countRemain', '>', 0);
        $query->filter->fieldValue('isDel', '=', false);
    }
}
