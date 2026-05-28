<?php

class Lib_OFC_Charts_Bar extends Lib_OFC_Charts_Base
{
    public function Lib_OFC_Charts_Bar()
    {
        parent::Lib_OFC_Charts_Base();

        $this->type = 'bar';
    }

    public function set_key($text, $size)
    {
        $this->text = iconv('windows-1251', 'utf-8', $text);
        $this->{'font-size'} = $size;
    }

    public function set_values($v)
    {
        $this->values = $v;
    }

    public function append_value($v)
    {
        $this->values[] = $v;
    }

    public function set_colour($colour)
    {
        $this->colour = $colour;
    }

    public function set_alpha($alpha)
    {
        $this->alpha = $alpha;
    }

    public function set_tooltip($tip)
    {
        $this->tip = iconv('windows-1251', 'utf-8', $tip);
    }
}
