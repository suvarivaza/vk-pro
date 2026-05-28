<?php

class Lib_OFC_Charts_Bar_Filled extends Lib_OFC_Charts_Bar
{
    public function Lib_OFC_Charts_Bar_Filled($colour = null, $outline_colour = null)
    {
        parent::Lib_OFC_Charts_Bar();

        $this->type = 'bar_filled';

        if (isset($colour)) {
            $this->set_colour($colour);
        }

        if (isset($outline_colour)) {
            $this->set_outline_colour($outline_colour);
        }
    }

    public function set_outline_colour($outline_colour)
    {
        $this->{'outline-colour'} = $outline_colour;
    }

    public function set_link($val, $link)
    {
        $this->link = [$val, $link];
    }
}
