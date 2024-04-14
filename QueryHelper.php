<?php
namespace FpDbTest;

class QueryHelper
{
    const VAL_TOKEN_PATTERN = '/\?[dfa\#]?/';

    public static function convertQuery(string $q, array $vals): string
    {
//        $quotesPattern = "/" . implode("|",array_map(
//                fn($ch) => "(${ch}(?:(?:[^${ch}])|(?:\\\\${ch}))*[^\\\\]${ch})",
//                ['"', "'", "`"]
//            )) . "/";
//        $quotesTokenizResult = new TokenizationResult($q, $quotesPattern);
//        $hash = new CustomHash(20, "?");
//        $quotesLessQ = implode($hash->getHash(), $quotesTokenizResult->getStrParts());
//        $condsTokenizResult = new TokenizationResult($quotesLessQ, "/[\{[^}]*\}]/");
//        Asserter::assertOk(strpos($));
        $result = self::processQuotes($q, function (string $s) {

        });
    }

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
        $hash = new CustomHash(20, "?'\"");
        $q = implode($hash->getHash(), $qs);
        $splittedByTokens = StringHelper::tokenize($q, $quotesPattern);
        $notQuotted = array_values(ArrayHelper::filterEven($splittedByTokens));
        $quotted = array_values(ArrayHelper::filterNotEven($splittedByTokens));
        $processedNotQuotted = StringHelper::resplit(
            $notQuotted,
            $hash->getHash(),
            fn($parts) => $f($parts)
        );
        $processedMixed = ArrayHelper::mix($processedNotQuotted, $quotted);
        return explode($hash->getHash(), implode("", $processedMixed));
    }

    public static function processCondition(string $q, callable $f): string
    {
        $pattern = "/[\\{(?:(?:[^\\}])|(?:[^\\{]))*\'}]/";
        $tokenized = StringHelper::tokenize($q, $pattern);

    }
}