<?php

class Lib_OFC_Elements_Axis_X extends Lib_OFC_Elements_Axis
{
    public function Lib_OFC_Elements_Axis_X()
    {
        parent::Lib_OFC_Elements_Axis();
    }

    public function set_stroke($stroke)
    {
        $this->stroke = $stroke;
    }

    public function set_tick_height($height)
    {
        $this->{'tick-height'} = $height;
    }

    // $o is a boolean
    public function set_offset($o)
    {
        $this->offset = ($o) ? true : false;
    }

    public function set_3d($val)
    {
        $this->{'3d'} = $val;
    }

    public function set_labels($x_axis_labels)
    {
        $this->labels = $x_axis_labels;
    }

    public function set_range($min, $max, $steps = 1)
    {
        $this->min = $min;
        $this->max = $max;
        $this->set_steps($steps);
    }

    /**
     * helper function to make the examples
     * simpler.
     */
    public function set_labels_from_array($a)
    {
        $x_axis_labels = new Lib_OFC_Elements_Axis_X_Label_Set();
        $x_axis_labels->set_labels($a);

        $this->labels = $x_axis_labels;

        if (isset($this->steps)) {
            $x_axis_labels->set_steps($this->steps);
        }
    }
}
