<?php

class Lib_OFC_Charts_Pie_Value
{
    public function Lib_OFC_Charts_Pie_Value($value, $text)
    {
        $this->value = $value;
        $this->label = iconv('windows-1251', 'utf-8', $text);
    }
}
