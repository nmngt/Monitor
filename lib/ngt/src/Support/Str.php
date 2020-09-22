<?php

namespace NGT\Support;

class Str
{
    public static function truncate($string, $length=100, $append="...", $wordwrap=false)
    {
        $string = trim($string);

        if ($wordwrap) {
            if (strlen($string) > $length) {
                $string = wordwrap($string, $length);
                $string = explode("\n", $string, 2);
                $string = $string[0] . $append;
            }
            return $string;
        } else {
            return (strlen($string)>$length) ? substr($string, 0, $length-strlen($append)).$append : $string;
        }
    }
}
