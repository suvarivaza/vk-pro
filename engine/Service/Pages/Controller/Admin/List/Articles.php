<?php

namespace Service\Pages;

class Controller_Admin_List_Articles extends Controller_Admin_List
{
    public function actionPrepare()
    {
        $this->_page = 'articles';
        $this->_title = 'Статьи';
        $this->_add = 'Добавить статью';

        return parent::actionPrepare();
    }
}
