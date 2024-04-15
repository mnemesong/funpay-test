<?php
namespace FpDbTest;

/**
 * Вспомогательный класс для работы со строками
 */
class StringHelper
{
    /**
     * @param string $str
     * @param string $pattern
     * @return array|string[]
     */
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
            $matches[0],
            fn($a, $b) => ($a[1] === $b[1])
                ? (mb_strlen($a[0]) - mb_strlen($b[0]))
                : $a[1] - $b[1]
        );
        if(empty($matches[0])) {
            return [$str];
        }
        $uniqIntervals = array_reduce(
            $matches[0],
            function ($acc, $el) use ($matches) {
                $newEl = [$el[1], $el[1] + mb_strlen($el[0])];
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
        AssertHelper::assertIsArrayOfStrings($result);
        return $result;
    }

    /**
     * @param string[] $strs - array count of N
     * @param string $delim
     * @param callable $process - process function (array count of M) -> (array count of M)
     * @return string[] - array count of N
     */
    public static function resplit(
        array $strs,
        string $delim,
        callable $process
    ): array {
        AssertHelper::assertIsArrayOfStrings($strs);
        $splitted = array_map(
            fn($s) => explode($delim, $s),
            $strs
        ); //string[][]
        $hashSkip = new CustomHash(20);
        $hashDelim = new CustomHash(20);
        $delimeters = array_values(ArrayHelper::rest(array_reduce(
            $splitted,
            fn($acc, $el) => array_merge(
                $acc,
                [$hashSkip->getHash()],
                array_fill(0, count($el) - 1, $hashDelim->getHash()),
            ),
            []
        )));
        $parts = array_reduce(
            $splitted,
            fn($acc, $el) => array_merge($acc, $el),
            []
        );
        $processedParts = $process($parts);
        AssertHelper::assertCountEq($parts, $processedParts);
        return array_reduce(
            ArrayHelper::mix($processedParts, $delimeters),
            function($acc, $el) use ($hashSkip, $hashDelim, $delim) {
                if(empty($acc)) {
                    return [$el];
                }
                if($el === $hashSkip->getHash()) {
                    return array_merge($acc, [""]);
                }
                if($el === $hashDelim->getHash()) {
                    return ArrayHelper::mapi(
                        $acc,
                        fn($v, $i) => ($i === array_key_last($acc))
                            ? ($v . $delim)
                            : $v
                    );
                }
                return ArrayHelper::mapi(
                    $acc,
                    fn($v, $i) => ($i === array_key_last($acc))
                            ? ($v . $el)
                            : $v
                );
            },
            []
        );
    }
}