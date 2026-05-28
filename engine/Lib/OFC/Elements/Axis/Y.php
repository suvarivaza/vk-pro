<?php

class Lib_OFC_Elements_Axis_Y extends Lib_OFC_Elements_Axis
{
    public function Lib_OFC_Elements_Axis_Y()
    {
        parent::Lib_OFC_Elements_Axis();
    }

    public function set_grid_colour($colour)
    {
        $this->{'grid-colour'} = $colour;
    }

    public function set_stroke($s)
    {
        $this->stroke = $s;
    }

    public function set_tick_length($val)
    {
        $this->{'tick-length'} = $val;
    }

    public function set_range($min, $max, $steps = 1)
    {
        $this->min = $min;
        $this->max = $max;
        $this->set_steps($steps);
    }

    public function set_offset($off)
    {
        $this->offset = ($off) ? 1 : 0;
    }

    public function set_labels($labels)
    {
        foreach ($labels as $l) {
            $labels_conv[] = iconv('windows-1251', 'utf-8', $l);
        }
        $this->labels = $labels_conv;
    }
}
