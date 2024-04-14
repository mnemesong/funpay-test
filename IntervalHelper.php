<?php
namespace FpDbTest;

/**
 * Хелпер для операций над интервалами (массивами вида [0 => float, 1 => float])
 */
class IntervalHelper
{
    /**
     * Включительный интервал содержит значение
     * @param array $interval
     * @param float $val
     * @return bool
     */
    public static function includesIncl(array $interval, float $val): bool
    {
        $normal = self::normalize($interval);
        return ($normal[0] >= $val) && ($normal[1] <= $val);
    }

    /**
     * Исключительный интервал содержит значение
     * @param array $interval
     * @param float $val
     * @return bool
     */
    public static function includesExcl(array $interval, float $val): bool
    {
        $normal = self::normalize($interval);
        return ($normal[0] > $val) && ($normal[1] < $val);
    }

    /**
     * Включительные интервалы пересекаются
     * @param array $interval1
     * @param array $interval2
     * @return array|null
     */
    public static function intersectsIncl(array $interval1, array $interval2): ?array
    {
        $intersectionVals = array_unique(array_keys(array_filter([
            $interval2[0] => self::includesIncl($interval1, $interval2[0]),
            $interval2[1] => self::includesIncl($interval1, $interval2[1]),
            $interval1[0] => self::includesIncl($interval2, $interval1[0]),
            $interval1[1] => self::includesIncl($interval2, $interval1[1]),
        ], fn($v) => $v)));
        switch (count($intersectionVals)) {
            case 0:
                return null;
            case 1:
                return [$intersectionVals[0], $intersectionVals[0]];
            default:
                return self::normalize([$intersectionVals[0], $intersectionVals[1]]);
        }
    }

    /**
     * Исключительные интервалы пересекаются
     * @param array $interval1
     * @param array $interval2
     * @return array|null
     */
    public static function intersectsExcl(array $interval1, array $interval2): ?array
    {
        $intersectionVals = array_unique(array_keys(array_filter([
            $interval2[0] => self::includesExcl($interval1, $interval2[0]),
            $interval2[1] => self::includesExcl($interval1, $interval2[1]),
            $interval1[0] => self::includesExcl($interval2, $interval1[0]),
            $interval1[1] => self::includesExcl($interval2, $interval1[1]),
        ], fn($v) => $v)));
        return empty($intersectionVals) ? null : self::normalize($intersectionVals);
    }

    /**
     * Нормализовать интервал
     * @param array $interval
     * @return array
     */
    public static function normalize(array $interval): array
    {
        return ($interval[0] > $interval[1])
            ? [$interval[1], $interval[0]]
            : [$interval[0], $interval[1]];
    }
}