<?php

class Lib_OFC_Charts_Scatter_Line extends Lib_OFC_Charts_Scatter
{
    public function Lib_OFC_Charts_Scatter_Line($colour, $dot_size)
    {
        parent::Lib_OFC_Charts_Scatter($colour, $dot_size);

        $this->type = 'scatter_line';
    }
}
