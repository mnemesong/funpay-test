<?php
namespace FpDbTest;

/**
 * Токенизирует специальые идентификаторы, форматирует и вставляет значения
 */
class TemplateTokenizer
{
    private DbFormatterInterface $dbFormatter;

    /**
     * @param DbFormatterInterface $dbFormatter
     */
    public function __construct(DbFormatterInterface $dbFormatter)
    {
        $this->dbFormatter = $dbFormatter;
    }

    /**
     * @param string $query
     * @param array $vals
     * @return string
     * @throws \Exception
     */
    public function formatAndInjectValues(string $query, array $vals): string
    {
        $result = new TokenizationResult($query, '/\?[dfa\#]?/');
        $tokens = $result->getTokens();
        $strParts = $result->getStrParts();
        Asserter::assertCount($vals, $tokens);
        $resultParts = [];
        foreach (array_values($tokens) as $i => $t) {
            $resultParts[] = $strParts[$i];
            $resultParts[] = self::formatValue($vals[$i], $t);
        }
        $resultParts[] = end($strParts);
        return implode("", $resultParts);
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
                Asserter::assertIsOptionScalar($val);
                return $this->dbFormatter->formatOptionScalarVal($val);
            case "?d":
                Asserter::assertOk(is_numeric($val) || is_null($val),
                    "should be numeric or null");
                return $this->dbFormatter->formatOptionScalarVal(intval($val));
            case "?f":
                Asserter::assertOk(is_numeric($val) || is_null($val),
                    "should be numeric or null");
                return $this->dbFormatter->formatOptionScalarVal(floatval($val));
            case "?a":
                Asserter::assertIsArrayOfOptionScalars($val);
                if(self::checkIsList($val)) {
                    return $this->dbFormatter->formatListOfVals($val);
                }
                return $this->dbFormatter->formatAssociativeArrayOfVals($val);
            case "?#":
                if(is_array($val)) {
                    Asserter::assertIsArrayOfStrings($val);
                    if(self::checkIsList($val)) {
                        return $this->dbFormatter->formatListOfFields($val);
                    }
                    return $this->dbFormatter->formatAssociativeArrayOfFields($val);
                }
                Asserter::assertIsString($val);
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