<?php
namespace FpDbTest;

use FpDbTest\DbFormatterInterface;

/**
 * Класс, форматирующий зачения для Mysql
 */
class MysqlDbFormatter implements DbFormatterInterface
{
    private \mysqli $mysqli;

    /**
     * @param \mysqli $mysqli
     */
    public function __construct(\mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    /**
     * @param scalar|null $val
     * @return string
     */
    public function formatOptionScalarVal($val): string
    {
        if($val === strval($val)) {
            return "'" . $this->mysqli->real_escape_string($val) . "'";
        }
        if(is_numeric($val)) {
            return strval($val);
        }
        if(is_null($val)) {
            return "NULL";
        }
        if(is_bool($val)) {
            if($val === true) {
                return "TRUE";
            }
            return "FALSE";
        }
        $print = print_r($val, true);
        throw new \InvalidArgumentException(
            "Value ${print} should be scalar");
    }

    /**
     * @param string $val
     * @return string
     */
    public function formatFieldName(string $val): string
    {
        return '`' . $this->mysqli->real_escape_string($val) . '`';
    }

    /**
     * @param scalar[]|null[] $val
     * @return string
     */
    public function formatListOfVals(array $val): string
    {
        return implode(", ", array_map(
            fn($v) => $this->formatOptionScalarVal($v),
            $val
        ));
    }

    /**
     * @param scalar[]|null[] $val
     * @return string
     */
    public function formatAssociativeArray(array $val): string
    {
        $result = [];
        foreach ($val as $i => $v) {
            $result[] =  $this->formatFieldName($i)
                . " = " . $this->formatOptionScalarVal($v);
        }
        return implode(", ", $result);
    }

    /**
     * @param string[] $val
     * @return string
     */
    public function formatListOfFields(array $val): string
    {
        return implode(", ", array_map(
            fn($v) => $this->formatFieldName($v),
            $val
        ));
    }

}