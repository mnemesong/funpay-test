<?php
namespace FpDbTest;

class CustomHashTest
{
    public static function abstractTest(array $forbiddenChars)
    {
        $forbStr = implode("", $forbiddenChars);
        for ($i = 0; $i < 1000; $i++) {
            $hashVal = (new CustomHash(mt_rand(5, 20), $forbStr))->getHash();
            foreach ($forbiddenChars as $fch) {
                AssertHelper::assertOk(mb_stripos($hashVal, $fch) === false,
                    "Hash '${hashVal}' should not contains char '${fch}'");
            }
        }
    }

    public static function testConstruct()
    {
        self::abstractTest(["'", "&", "?", "1", "h"]);
        self::abstractTest(['"', "`", "g", "*", "?"]);
    }

    public static function runAll()
    {
        self::testConstruct();
    }
}