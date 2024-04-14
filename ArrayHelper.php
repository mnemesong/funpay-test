<?php
namespace FpDbTest;

/**
 * Вспомогательный класс для работы с массивами
 */
class ArrayHelper
{
    /**
     * Mix values of two arrays: one over one
     * @param array $outer
     * @param array $inner
     * @return array
     */
    public static function mix(array $outer, array $inner): array
    {
        Asserter::assertOk(
            count($outer) === (count($inner) + 1),
            "count of outer values should be more on 1 then "
            . "count of inner values: \n" . print_r($outer, true)
            . "\n". print_r($inner, true)
        );
        $outerVals = array_values($outer);
        $resultParts = [];
        foreach (array_values($inner) as $i => $v) {
            $resultParts[] = $outerVals[$i];
            $resultParts[] = $v;
        }
        $resultParts[] = end($outerVals);
        return $resultParts;
    }

    /**
     * Zip two arrays to array of tuples
     * @param array $arr1 - T1[]
     * @param array $arr2 - T2[]
     * @return array - [T1, T2][]
     */
    public static function zip(array $arr1, array $arr2): array
    {
        Asserter::assertOk(
            count($arr1) === (count($arr2)),
            "count of two arrays should be equals: \n"
                . print_r($arr1, true)
                . "\n" . print_r($arr2, true)
        );
        $result = [];
        $arr2Vals = array_values($arr2);
        foreach (array_values($arr1) as $i => $v) {
            $result[] = [$v, $arr2Vals[$i]];
        }
        return $result;
    }

    /**
     * @param array $array
     * @return array
     */
    public static function filterEven(array $array): array
    {
        return array_filter(
            array_values($array),
            fn($k) => ($k % 2) === 0, ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * @param array $array
     * @return array
     */
    public static function filterNotEven(array $array): array
    {
        return array_filter(
            array_values($array),
            fn($k) => ($k % 2) !== 0, ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * @param array $array - A[]
     * @param callable $f - convertion function :: A -> int -> B
     * @return array - B[]
     */
    public static function mapi(array $array, callable $f): array
    {
        $result = [];
        foreach ($array as $k => $v) {
            $result[$k] = $f($v, $k);
        }
        return $result;
    }

    public static function rest(array $arr): array
    {
        $firstI = array_key_first($arr);
        $result = [];
        foreach ($arr as $i => $k) {
            if($i !== $firstI) {
                $result[$i] = $k;
            }
        }
        return $result;
    }
}