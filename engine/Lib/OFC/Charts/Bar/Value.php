<?php

class Lib_OFC_Charts_Bar_Value
{
    public function Lib_OFC_Charts_Bar_Value($top, $bottom = null)
    {
        $this->top = $top;

        if (isset($bottom)) {
            $this->bottom = $bottom;
        }
    }

    public function set_colour($colour)
    {
        $this->colour = $colour;
    }

    public function set_tooltip($tip)
    {
        $this->tip = iconv('windows-1251', 'utf-8', $tip);
    }
}
