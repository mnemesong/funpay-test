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
     * @param array $val2
     * @return void
     */
    public static function assertArrEq(
        array $val1,
        array $val2
    ): void {
        $keys1 = array_keys($val1);
        $keys2 = array_keys($val2);
        $throwNewErr = function ($val1, $val2) {
            $print1 = print_r($val1, true);
            $print2 = print_r($val2, true);
            throw new \Error("${print1} and ${print2} are not equal");
        };
        if(count($keys2) !== count($keys1)) {
            $throwNewErr($val1, $val2);
        }
        foreach ($keys1 as $k) {
            if(!in_array($k, $keys2, true)) {
                $throwNewErr($val1, $val2);
            }
            if($val1[$k] !== $val2[$k]) {
                $throwNewErr($val1, $val2);
            }
        }
    }

    /**
     * @param array $val1
     * @param array $val2
     * @return void
     */
    public static function assertCount(array $val1, array $val2): void
    {
        $cnt1 = count($val1);
        $cnt2 = count($val2);
        if(count($val1) !== count($val2)) {
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
}