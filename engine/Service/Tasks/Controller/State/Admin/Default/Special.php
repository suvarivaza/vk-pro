<?php

namespace Service\Tasks;

class Controller_State_Admin_Default_Special extends Controller_State_Admin_Default
{
    protected $_url = '/admin/tasks/list/special?p=@p@';
    protected $_title = 'Спецзадания';

    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        if (isset($this->_application->menu['tasks']['menu']['special'])) {
            $this->_application->menu['tasks']['menu']['all']['active'] = false;
            $this->_application->menu['tasks']['menu']['special']['active'] = true;
        }

        return null;
    }

    protected function _filter(\Lib_ORM_Query $query)
    {
        parent::_filter($query);
        $query->filter->fieldValue('isSpecial', '=', true);
    }
}
