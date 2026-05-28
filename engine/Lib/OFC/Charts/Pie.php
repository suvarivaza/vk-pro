<?php

/**
 * @property string $tip
 * @property $animate
 */
class Lib_OFC_Charts_Pie extends Lib_OFC_Charts_Base
{
    public function Lib_OFC_Charts_Pie()
    {
        parent::Lib_OFC_Charts_Base();

        $this->type = 'pie';
        $this->colours = ['#d01f3c', '#356aa0', '#C79810'];
        $this->alpha = 0.6;
        $this->border = 2;
        $this->values = [2, 3, new Lib_OFC_Charts_Pie_Value(6.5, 'hello (6.5)')];
    }

    // boolean
    public function set_animate($v)
    {
        $this->animate = $v;
    }

    // real
    public function set_start_angle($angle)
    {
        $this->{'start-angle'} = $angle;
    }
}
