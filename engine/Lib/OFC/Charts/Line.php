<?php

class Lib_OFC_Charts_Line extends Lib_OFC_Charts_Base
{
    public function Lib_OFC_Charts_Line()
    {
        parent::Lib_OFC_Charts_Base();

        $this->type = 'line';
    }

    public function set_values($v)
    {
        $this->values = $v;
    }

    public function set_width($width)
    {
        $this->width = $width;
    }

    public function set_colour($colour)
    {
        $this->colour = $colour;
    }

    public function set_dot_size($size)
    {
        $this->{'dot-size'} = $size;
    }

    public function set_halo_size($size)
    {
        $this->{'halo-size'} = $size;
    }

    public function set_key($text, $font_size)
    {
        $this->text = iconv('windows-1251', 'utf-8', $text);
        $this->{'font-size'} = $font_size;
    }

    public function set_tooltip($tip)
    {
        $this->tip = iconv('windows-1251', 'utf-8', $tip);
    }
}
