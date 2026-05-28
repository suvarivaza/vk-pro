<?php

namespace Service\Tasks;

class Controller_State_Admin_List_Done extends Controller_State_Admin_Default
{
    protected $_url = '/admin/tasks/list/done?p=@p@';
    protected $_title = 'Завершенные задания';

    protected function _filter(\Lib_ORM_Query $query)
    {
        parent::_filter($query);
        $query->filter->fieldValue('countRemain', '=', 0);
    }
}
