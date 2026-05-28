<?php
/**
 * PHP Integration of Open Flash Chart
 * Copyright (C) 2008 John Glazebrook <open-flash-chart@teethgrinder.co.uk>
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */
class Lib_OFC_Elements_Axis_X_Label extends Lib_OFC_Elements_Base
{
    public function Lib_OFC_Elements_Axis_X_Label($text, $colour, $size, $rotate)
    {
        parent::Lib_OFC_Elements_Base();

        $this->set_text($text);
        $this->set_colour($colour);
        $this->set_size($size);
        $this->set_rotate($rotate);
    }

    public function set_text($text)
    {
        $this->text = $text;
    }

    public function set_colour($colour)
    {
        $this->colour = $colour;
    }

    public function set_size($size)
    {
        $this->size = $size;
    }

    public function set_rotate($rotate)
    {
        $this->rotate = $rotate;
    }

    public function set_vertical()
    {
        $this->rotate = 'vertical';
    }

    public function set_visible()
    {
        $this->visible = true;
    }
}
