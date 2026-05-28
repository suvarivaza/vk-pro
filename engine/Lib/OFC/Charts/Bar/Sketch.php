<?php

class Lib_OFC_Charts_Bar_Sketch extends Lib_OFC_Charts_Bar
{
    public function Lib_OFC_Charts_Bar_Sketch($colour, $outline_colour, $fun_factor)
    {
        parent::Lib_OFC_Charts_Bar();

        $this->type = 'bar_sketch';

        $this->set_colour($colour);
        $this->set_outline_colour($outline_colour);
        $this->offset = $fun_factor;
    }

    public function set_outline_colour($outline_colour)
    {
        $this->{'outline-colour'} = $outline_colour;
    }
}
