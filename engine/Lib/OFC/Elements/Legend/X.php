<?php

class Lib_OFC_Elements_Legend_X extends Lib_OFC_Elements_Base
{
    public function Lib_OFC_Elements_Legend_X($text = '')
    {
        parent::Lib_OFC_Elements_Base();

        $this->text = iconv('windows-1251', 'utf-8', $text);
    }
}
