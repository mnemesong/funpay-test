<?php
namespace FpDbTest;

class QueryProcessor
{
    private DbFormatterInterface $dbFormatter;
    private CustomHash $skipHash;

    /**
     * @param DbFormatterInterface $dbFormatter
     */
    public function __construct(DbFormatterInterface $dbFormatter)
    {
        $this->dbFormatter = $dbFormatter;
        $this->skipHash = new CustomHash(30);
    }

    /**
     * @return string
     */
    public function getSkipVal(): string
    {
        return $this->skipHash->getHash();
    }

    /**
     * @param string $q
     * @param array $vals_
     * @return string
     */
    public function processQuery(string $q, array $vals_): string
    {
        $vals = array_values($vals_);
        return current(QueryHelper::processQuotes(
            [$q],
            fn($quotelessParts) => QueryHelper::processCondition(
                $quotelessParts,
                function($condSplitted) use ($vals) {
                    //Распределяем части запроса по парам:
                    //  (часть запроса, относящиеся к нему значения)
                    $valsDistribution = array_reduce(
                        $condSplitted,
                        function ($acc, $el) use ($vals) {
                            $currentValsOffset = array_reduce(
                                $acc,
                                fn($acc_, $el_) => $acc_ + count($el_[1]),
                                0
                            );
                            $currentVals = array_slice(
                                $vals,
                                $currentValsOffset,
                                QueryHelper::calcNumOfValueTokens($el)
                            );
                            return array_merge(
                                $acc,
                                [[$el, $currentVals]],
                            );
                        },
                        [] //[condSplittedPart, val[]][]
                    );

                    //Удаляем условия со СКИПом
                    $groupsCleanedDistribution = ArrayHelper::mapi(
                        $valsDistribution,
                        function ($v, $i) {
                            if(in_array($this->skipHash->getHash(), $v[1], true)) {
                                AssertHelper::assertOk(($i % 2) !== 0,
                                    "Found SKIP value in not condition "
                                    . "query part: " . $v[0]);
                                return ["", []];
                            }
                            return $v;
                        }
                    );

                    //Вставляем значения
                    return array_map(
                        fn($v /* [condSplittedPart, val[]] */) =>
                            QueryHelper::processValueTokens(
                                [$v[0]],
                                fn(array $tokenSplittedParts) => ArrayHelper::mapi(
                                    $tokenSplittedParts,
                                    // строки с/без заченим можно определить по четности
                                    fn($part, $i) => (($i % 2) === 0)
                                        ? $part
                                        : $this->formatValue(
                                            $v[1][floor($i / 2)], //значение
                                            $part //токен
                                        )
                                )
                            )[0],
                        $groupsCleanedDistribution
                    );
                }
            )
        ));
    }

    /**
     * @param $val
     * @param string $token
     * @return string
     * @throws \Exception
     */
    private function formatValue($val, string $token): string
    {
        switch ($token) {
            case "?":
                AssertHelper::assertIsOptionScalar($val);
                return $this->dbFormatter->formatOptionScalarVal($val);
            case "?d":
                AssertHelper::assertIsOptionScalar($val);
                return $this->dbFormatter->formatOptionScalarVal(intval($val));
            case "?f":
                AssertHelper::assertIsOptionScalar($val);
                return $this->dbFormatter->formatOptionScalarVal(floatval($val));
            case "?a":
                AssertHelper::assertIsArrayOfOptionScalars($val);
                if(self::checkIsList($val)) {
                    return $this->dbFormatter->formatListOfVals($val);
                }
                return $this->dbFormatter->formatAssociativeArray($val);
            case "?#":
                if(is_array($val)) {
                    AssertHelper::assertIsArrayOfStrings($val);
                    if(self::checkIsList($val)) {
                        return $this->dbFormatter->formatListOfFields($val);
                    }
                    return $this->dbFormatter->formatAssociativeArray($val);
                }
                AssertHelper::assertIsString($val);
                return $this->dbFormatter->formatFieldName($val);
            default:
                throw new \Exception("Unknown token: " . print_r($val, true));
        }
    }

    /**
     * @param array $arr
     * @return bool
     */
    private static function checkIsList(array $arr): bool
    {
        $cnt = count($arr);
        if ($cnt === 0) {
            return true;
        }
        return array_keys($arr) === range(0, $cnt - 1);
    }
}