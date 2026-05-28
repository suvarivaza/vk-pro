<?php

class Lib_OFC_Elements_Axis extends Lib_OFC_Elements_Base
{
    public function Lib_OFC_Elements_Axis()
    {
        parent::Lib_OFC_Elements_Base();
    }

    public function set_colours($colour, $grid_colour)
    {
        $this->set_colour($colour);
        $this->set_grid_colour($grid_colour);
    }

    public function set_colour($colour)
    {
        $this->colour = $colour;
    }

    public function set_grid_colour($colour)
    {
        $this->{'grid-colour'} = $colour;
    }

    public function set_steps($steps = 1)
    {
        $this->steps = $steps;
    }
}
