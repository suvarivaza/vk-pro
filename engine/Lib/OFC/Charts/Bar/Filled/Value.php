<?php

class Lib_OFC_Charts_Bar_Filled_Value extends Lib_OFC_Charts_Bar_Value
{
    public function Lib_OFC_Charts_Bar_Filled_Value($val, $colour)
    {
        parent::Lib_OFC_Charts_Bar_Value($val, $colour);
    }

    public function set_outline_colour($outline_colour)
    {
        $this->{'outline-colour'} = $outline_colour;
    }
}
