<?php
namespace FpDbTest;

class QuotesTokenizerTest
{
    /**
     * @return void
     */
    public static function test1()
    {
        $pattern = '/\?[dfa\#]?/';
        $tokenizer = new QuotesTokenizer($pattern);
        $tokenReqs = $tokenizer->tokenize(new TokenizationRequest(
            'SELECT ?# FROM `users` WHERE user_name = "Jack" AND block = ?',
            ['*', 12]
        ));
        $assertReq = function (TokenizationRequest $tr, array $result) {
            Asserter::assertArraysOfOptionScalarEquals(array_merge(
                [$tr->getQuery()],
                $tr->getVals()
            ), $result);
        };
        Asserter::assertCount($tokenReqs, 5);
        $assertReq($tokenReqs[0], ['SELECT ?# FROM ', '*']);
        $assertReq($tokenReqs[1], ['`users`']);
        $assertReq($tokenReqs[2], [' WHERE user_name = ']);
        $assertReq($tokenReqs[3], ['"Jack"']);
        $assertReq($tokenReqs[4], [' AND block = ?', 12]);
    }

    /**
     * @return void
     */
    public static function runAll()
    {
        self::test1();
    }
}