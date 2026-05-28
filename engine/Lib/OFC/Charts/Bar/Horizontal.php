<?php

class Lib_OFC_Charts_Bar_Horizontal
{
    public function Lib_OFC_Charts_Bar_Horizontal()
    {
        $this->type = 'hbar';
        $this->colour = '#9933CC';
        $this->text = 'Page views';

        $this->{'font-size'} = 10;
        $this->values = [];
    }

    public function append_value($v)
    {
        $this->values[] = $v;
    }
}
