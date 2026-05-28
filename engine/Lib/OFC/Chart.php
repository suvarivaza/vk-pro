<?php

/**
 * @property $x_axis
 * @property $y_axis
 * @property $y_axis_right
 * @property $x_legend
 * @property $y_legend
 * @property $bg_colour
 */
class Lib_OFC_Chart
{
    public function Lib_OFC_Chart()
    {
        $this->title = new Lib_OFC_Elements_Title('Many data lines');
        $this->elements = [];
    }

    public function set_title($t)
    {
        $this->title = $t;
    }

    public function set_x_axis($x)
    {
        $this->x_axis = $x;
    }

    public function set_y_axis($y)
    {
        $this->y_axis = $y;
    }

    public function add_y_axis($y)
    {
        $this->y_axis = $y;
    }

    public function set_y_axis_right($y)
    {
        $this->y_axis_right = $y;
    }

    public function add_element($e)
    {
        $this->elements[] = $e;
    }

    public function set_x_legend($x)
    {
        $this->x_legend = $x;
    }

    public function set_y_legend($y)
    {
        $this->y_legend = $y;
    }

    public function set_bg_colour($colour)
    {
        $this->bg_colour = $colour;
    }

    public function toString()
    {
        return json_encode($this);
    }

    public function toPrettyString()
    {
        return Lib_OFC_Json_Format::json_format($this->toString());
    }
}
