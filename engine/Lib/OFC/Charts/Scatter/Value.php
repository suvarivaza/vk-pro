<?php

class Lib_OFC_Charts_Scatter_Value
{
    public function Lib_OFC_Charts_Scatter_Value($x, $y, $dot_size = -1)
    {
        $this->x = $x;
        $this->y = $y;

        if ($dot_size > 0) {
            $this->{'dot-size'} = $dot_size;
        }
    }
}
