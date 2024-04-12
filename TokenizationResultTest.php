<?php

namespace FpDbTest;
use FpDbTest\Asserter;
use FpDbTest\TokenizationResult;

class TokenizationResultTest
{
    public static function test1()
    {
        $given = new TokenizationResult(
            'SELECT ?# FROM users WHERE user_id = ?d AND block = ?',
            '/\?[dfa\#]?/'
        );

        $result1 = $given->getTokens();
        $nominal1 = [7 => "?#", 37 => "?d", 52 => "?"];
        Asserter::assertArrEq($result1, $nominal1);

        $result2 = $given->getStrParts();
        $nominal2 = ["SELECT ", " FROM users WHERE user_id = ", " AND block = ", ""];
        Asserter::assertArrEq($result2, $nominal2);
    }

    public static function runAll()
    {
        self::test1();
    }
}