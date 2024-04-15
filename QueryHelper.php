<?php
namespace FpDbTest;

class QueryHelper
{
    /**
     * Extract quoted values and process other parts of query
     * @param array $qs
     * @param callable $f - function of processing query parts :: string[] -> string[]
     * @return string[]
     */
    public static function processQuotes(
        array $qs,
        callable $f
    ): array {
        $quotesPattern = "/" . implode("|",array_map(
                fn($ch) => "(${ch}(?:(?:[^${ch}])|(?:\\\\${ch}))*[^\\\\]${ch})",
                ['"', "'", "`"]
            )) . "/";
        $hash = new CustomHash(20, "?'`" . '"');
        $q = implode($hash->getHash(), $qs);
        $splittedByTokens = StringHelper::tokenize($q, $quotesPattern);
        AssertHelper::assertIsArrayOfStrings($splittedByTokens);
        $notQuotted = array_values(ArrayHelper::filterEven($splittedByTokens));
        AssertHelper::assertStringNotContainsSubstrings(
            implode("", $notQuotted), ["'", '"', '`']);
        $quotted = array_values(ArrayHelper::filterNotEven($splittedByTokens));
        $processedNotQuotted = StringHelper::resplit(
            $notQuotted,
            $hash->getHash(),
            fn($parts) => $f($parts)
        );
        $processedMixed = ArrayHelper::mix($processedNotQuotted, $quotted);
        $result = explode($hash->getHash(), implode("", $processedMixed));
        AssertHelper::assertCountEq($qs, $result);
        return $result;
    }

    /**
     * @param string[] $qs
     * @param callable $f
     * @return string[]
     */
    public static function processCondition(array $qs, callable $f): array
    {
        $pattern = "/\\{[^\\}\\{]*\\}/";
        $hash = new CustomHash(20, "?{}");
        $q = implode($hash->getHash(), $qs);
        $withConditionsExcluded = StringHelper::tokenize($q, $pattern);
        $conditionLess = array_values(
            ArrayHelper::filterEven($withConditionsExcluded));
        $conditions = array_values(
            ArrayHelper::filterNotEven($withConditionsExcluded));
        AssertHelper::assertStringNotContainsSubstrings(
            implode("", $conditionLess), ["{", '}']);
        $conditionsWithoutBrackets = array_map(
            fn($c) => implode("", array_filter(
                mb_str_split($c),
                fn($i) => (($i !== 0) && ($i !== (mb_strlen($c) - 1))),
                ARRAY_FILTER_USE_KEY
            )),
            $conditions
        );
        $processed = StringHelper::resplit(
            ArrayHelper::mix($conditionLess, $conditionsWithoutBrackets),
            $hash->getHash(),
            fn($parts) => $f($parts)
        );
        $processedBracketlessConds = ArrayHelper::filterNotEven($processed);
        $processedConditionless = ArrayHelper::filterEven($processed);
        $processedConds = array_map(
            fn($s) => "{" . $s . "}",
            $processedBracketlessConds
        );
        $result = explode(
            $hash->getHash(),
            implode("", ArrayHelper::mix($processedConditionless, $processedConds))
        );
        AssertHelper::assertCountEq($qs, $result);
        return $result;
    }

    /**
     * @param string[] $qs
     * @param callable $f
     * @return array
     */
    public static function processValueTokens(
        array $qs,
        callable $f
    ): array {
        $pattern =  '/\?[dfa\#]?/';
        $hash = new CustomHash(20, "?");
        $q = implode($hash->getHash(), $qs);
        $splitted = StringHelper::tokenize($q, $pattern);
        $valuesLess = array_values(
            ArrayHelper::filterEven($splitted));
        AssertHelper::assertStringNotContainsSubstrings(
            implode("", $valuesLess), ["?"]);
        $processed = StringHelper::resplit(
            $splitted,
            $hash->getHash(),
            fn ($parts) => $f($parts)
        );
        $result = explode(
            $hash->getHash(),
            implode("", $processed)
        );
        AssertHelper::assertCountEq($qs, $result);
        return $result;
    }

    /**
     * @param string $q
     * @return int
     */
    public static function calcNumOfValueTokens(string $q): int
    {
        $pattern =  '/\?[dfa\#]?/';
        $splitted = StringHelper::tokenize($q, $pattern);
        return floor(count($splitted) / 2);
    }
}