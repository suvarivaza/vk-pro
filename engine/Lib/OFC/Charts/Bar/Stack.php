<?php

class Lib_OFC_Charts_Bar_Stack extends Lib_OFC_Charts_Bar
{
    public function Lib_OFC_Charts_Bar_Stack()
    {
        parent::Lib_OFC_Charts_Bar();

        $this->type = 'bar_stack';
    }

    public function append_stack($v)
    {
        $this->append_value($v);
    }
}
