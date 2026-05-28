<?php

namespace Service\Pages;

class Controller_Admin_List_News extends Controller_Admin_List
{
    public function actionPrepare()
    {
        $this->_page = 'news';
        $this->_title = 'Новости';
        $this->_add = 'Добавить новость';

        return parent::actionPrepare();
    }
}
