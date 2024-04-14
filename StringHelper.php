<?php
namespace FpDbTest;

class StringHelper
{
    public static function tokenize(string $str, string $pattern): array
    {
        $matches = [];
        preg_match_all(
            $pattern,
            $str,
            $matches,
            PREG_OFFSET_CAPTURE
        );
        uasort(
            $matches,
            fn($a, $b) => ($a[1] === $b[1])
                ? (strlen($a[0] - strlen($b[0])))
                : $a[1] - $b[1]
        );
        $uniqIntervals = array_reduce(
            $matches[0],
            function ($acc, $el) use ($matches) {
                $newEl = [$el[1], $el[1] + strlen($el[0])];
                if(empty($acc)) {
                    return [$newEl];
                }
                if(array_search($newEl, $acc) !== false) {
                    return $acc;
                }
                $intersects = array_filter(
                    $acc,
                    fn($interv) => IntervalHelper::intersectsExcl($interv, $newEl)
                );
                if(empty($intersects)) {
                    return array_merge($acc, [$newEl]);
                }
                if($intersects === $newEl) {
                    return $acc;
                }
                throw new \Error("Pattern matches are intersects: "
                    . print_r($matches, true));
            },
            [] //[strlen, position][]
        );
        $splitPoints = array_merge([0], array_values(array_reduce(
            $uniqIntervals,
            fn($acc, $el) => array_merge($acc, IntervalHelper::normalize($el)),
            []
        )));
        $exploded = mb_str_split($str);
        $result = [];
        foreach ($splitPoints as $k => $v) {
            if($k < count($splitPoints) - 1) {
                $result[] = implode("", array_slice(
                    $exploded,
                    $v,
                    $splitPoints[$k + 1] - $v
                ));
            }
        }
        $result[] = implode("",
            array_slice($exploded, end($splitPoints)));
        return $result;
    }
}