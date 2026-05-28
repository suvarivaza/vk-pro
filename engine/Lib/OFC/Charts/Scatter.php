<?php

class Lib_OFC_Charts_Scatter extends Lib_OFC_Charts_Base
{
    public function Lib_OFC_Charts_Scatter($colour, $dot_size)
    {
        parent::Lib_OFC_Charts_Base();

        $this->type = 'scatter';
        $this->set_colour($colour);
        $this->set_dot_size($dot_size);
    }

    public function set_colour($colour)
    {
        $this->colour = $colour;
    }

    public function set_dot_size($dot_size)
    {
        $this->{'dot-size'} = $dot_size;
    }

    public function set_values($values)
    {
        $this->values = $values;
    }
}
