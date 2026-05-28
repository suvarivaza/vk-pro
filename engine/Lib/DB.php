<?php

class Lib_DB
{
    public static function NormalizeFloat($float)
    {
        $r = localeconv();

        return str_replace($r['decimal_point'], '.', '' . $float);
    }
}
