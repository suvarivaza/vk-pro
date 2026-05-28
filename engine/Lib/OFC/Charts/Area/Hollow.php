<?php

class Lib_OFC_Charts_Area_Hollow extends Lib_OFC_Charts_Area
{
    public function Lib_OFC_Charts_Area_Hollow()
    {
        parent::Lib_OFC_Charts_Area();

        $this->type = 'area_hollow';

        $this->{'fill-alpha'} = 0.35;

        $this->values = [];
    }

    public function set_width($w)
    {
        $this->width = $w;
    }

    public function set_colour($colour)
    {
        $this->colour = $colour;
    }

    public function set_values($v)
    {
        $this->values = $v;
    }

    public function set_dot_size($size)
    {
        $this->{'dot-size'} = $size;
    }

    public function set_key($text, $font_size)
    {
        $this->text = iconv('windows-1251', 'utf-8', $text);
        $this->{'font-size'} = $font_size;
    }
}
