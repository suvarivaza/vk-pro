<?php

namespace Service\Pages;

class Controller_Client_List_News extends Controller_Client_List
{
    public function actionPrepare()
    {
        $this->_page = 'news';

        return parent::actionPrepare();
    }
}
