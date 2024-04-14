<?php
namespace FpDbTest;

/**
 * Вспомогательный класс для проверки утверждений на тестах
 */
final class Asserter
{
    /**
     * @param mixed $val
     * @param string $msg
     * @return void
     */
    public static function assertOk($val, string $msg = "is not trueable"): void
    {
        if(!$val) {
            $printed = print_r($val, true);
            throw new \Error("Value ${printed} ${msg}");
        }
    }

    /**
     * @param scalar|null $val1
     * @param scalar|null $val2
     * @return void
     */
    public static function assertOptionScalarEq($val1, $val2): void
    {
        self::assertIsOptionScalar($val1);
        self::assertIsOptionScalar($val2);
        if($val1 !== $val2) {
            $printed1 = print_r($val1, true);
            $printed2 = print_r($val2, true);
            throw new \Error(
                "Values ${printed1} and ${printed2} are not equal");
        }
    }

    /**
     * @param array $val1
     * @param int $nominalCnt
     * @return void
     */
    public static function assertCount(array $val1, int $nominalCnt): void
    {
        $cnt = count($val1);
        if($cnt !== $nominalCnt) {
            $print1 = print_r($val1, true);
            throw new \Error(
                "Count ${cnt} is not equal ${nominalCnt} for ${print1}");
        }
    }

    /**
     * @param array $val1
     * @param array $val2
     * @return void
     */
    public static function assertCountEq(array $val1, array $val2): void
    {
        $cnt1 = count($val1);
        $cnt2 = count($val2);
        if($cnt1 !== $cnt2) {
            $print1 = print_r($val1, true);
            $print2 = print_r($val2, true);
            throw new \Error(
                "Count of ${print1} is not equal count of ${print2}: "
                    . "${cnt1} !== ${cnt2}");
        }
    }

    /**
     * @param $val
     * @return void
     */
    public static function assertIsOptionScalar($val): void
    {
        self::assertOk(is_scalar($val) || is_null($val),
            "should be scalar or null");
    }

    /**
     * @param $val
     * @return void
     */
    public static function assertIsString($val): void
    {
        self::assertOk(strval($val) === $val, "should be string");
    }

    /**
     * @param $val
     * @return void
     */
    public static function assertIsArrayOfOptionScalars($val): void
    {
        self::assertOk(is_array($val), "should be array");
        foreach ($val as $v) {
            self::assertIsOptionScalar($v);
        }
    }

    /**
     * @param $val
     * @return void
     */
    public static function assertIsArrayOfStrings($val): void
    {
        self::assertOk(is_array($val), "should be array");
        foreach ($val as $v) {
            self::assertIsString($v);
        }
    }

    /**
     * @param scalar[]|null[] $arr1
     * @param scalar[]|null[] $arr2
     * @return void
     */
    public static function assertArraysOfOptionScalarEquals(
        array $arr1,
        array $arr2
    ): void {
        Asserter::assertIsArrayOfOptionScalars($arr1);
        Asserter::assertIsArrayOfOptionScalars($arr2);
        Asserter::assertCountEq($arr1, $arr2);
        $arr2Keys = array_keys($arr2);
        foreach (array_keys($arr1) as $k) {
            if(!in_array($k, $arr2Keys) || ($arr1[$k] !== $arr2[$k])) {
                throw new \Error("Arrays are not equal:\n"
                    . print_r($arr1, true) . " !== "
                    . print_r($arr2, true));
            }
        }
    }

    /**
     * @param array $array
     * @param callable $f
     * @param string $desc
     * @return void
     */
    public static function assertEvery(
        array $array,
        callable $f,
        string $desc = "match condition"
    ): void {
        foreach ($array as $v) {
            if(!$f($v)) {
                $arrPrint = print_r($array, true);
                $vPrint = print_r($v, true);
                throw new \Error("Element element${vPrint} is not "
                    . "${desc} in ${arrPrint}");
            }
        }
    }
}