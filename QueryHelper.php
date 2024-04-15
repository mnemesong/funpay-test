<?php
namespace FpDbTest;

/**
 * Вспомогательный класс:
 * содержит чистые методы токенизации и преобразования частей SQL-запросов
 */
class QueryHelper
{
    /**
     * Выделяет значения в кавычках и отдает остальную часть запроса
     * функции преобразования
     * @param string[] $qs - string[]M
     * @param callable $f - function of processing query parts :: string[]N -> string()[]N
     * @return string[] - string[]M
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
     * Разбивает части запроса по фигурным скобкам и отдает по частям
     * функции преобразования в таком виде, что все нечетные части запроса
     * были включены в условное выражение
     * @param string[] $qs - string[]M
     * @param callable $f - function of processing query parts :: string[]N -> string()[]N
     * @return string[] - string[]M
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
        $result = explode(
            $hash->getHash(),
            implode("", $processed)
        );
        AssertHelper::assertCountEq($qs, $result);
        return $result;
    }

    /**
     * Разбивает части запроса по токенам вставки значений и отдает на обработку
     * функции в таком виде, что все нечетные части запроса - токены вставки значений
     * @param string[] $qs - string[]M
     * @param callable $f - function of processing query parts :: string[]N -> string()[]N
     * @return string[] - string[]M
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
     * Считает кол-во токенов вставки значений в части запроса
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