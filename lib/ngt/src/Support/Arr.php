<?php declare(strict_types=1);

namespace NGT\Support;

class Arr
{
    /**
     * zips an array..
     *
     * $a = array(1, 4, 7);
     * $b = array(2, 5, 8);
     * $c = array(3, 6, 9);
     *
     * var_dump(implode(', ', array_zip($a, $b, $c)));
     *
     * // Outputs:
     * string(25) "1, 2, 3, 4, 5, 6, 7, 8, 9"
     *
     * @param  arrays $arrays [description]
     * @return [type]         [description]
     */
    public static function zip(...$arrays) : ?string
    {
        return array_merge(...array_map(null, ...$arrays));
    }
}
