<?php

class Lib_OFC_Elements_Title extends Lib_OFC_Elements_Base
{
    public function Lib_OFC_Elements_Title($text = '')
    {
        parent::Lib_OFC_Elements_Base();

        $this->text = iconv('windows-1251', 'utf-8', $text);
    }
}
