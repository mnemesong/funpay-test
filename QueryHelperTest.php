<?php
namespace FpDbTest;

class QueryHelperTest
{
    /**
     * @return void
     */
    public static function testProcessQuotes()
    {
        $givenQuery = [
            'SELECT ?# FROM `users` ',
            'WHERE user_id = ?d AND block = "dsaa"'
        ];
        $result = QueryHelper::processQuotes(
            $givenQuery,
            fn($strParts) => array_map(fn($p) => "!" . $p, $strParts)
        );
        AssertHelper::assertArraysOfOptionScalarEquals( $result, [
            '!SELECT ?# FROM `users`! ',
            '!WHERE user_id = ?d AND block = "dsaa"!'
        ]);
    }

    /**
     * @return void
     */
    public static function testProcessCondition()
    {
        $givenQuery = [
            'SELECT ?# FROM `users` ',
            'WHERE user_id = ?d { AND block = "dsaa" }'
        ];
        $result = QueryHelper::processCondition(
            $givenQuery,
            fn($strParts) => array_map(fn($p) => "!" . $p, $strParts)
        );
        AssertHelper::assertArraysOfOptionScalarEquals($result, [
            '!SELECT ?# FROM `users` ',
            '!WHERE user_id = ?d ! AND block = "dsaa" !'
        ]);
    }

    /**
     * @return void
     */
    public static function testProcessValueTokens()
    {
        $givenQuery = [
            'SELECT ?# FROM `users` ',
            'WHERE user_id = ?d { AND block = "dsaa" }'
        ];
        $result = QueryHelper::processValueTokens(
            $givenQuery,
            fn($strParts) => array_map(
                fn($p) =>(in_array($p, ["?", "?d", "?#"])) ? "!" : $p
                , $strParts
            )
        );
        AssertHelper::assertArraysOfOptionScalarEquals($result, [
            'SELECT ! FROM `users` ',
            'WHERE user_id = ! { AND block = "dsaa" }'
        ]);
    }

    /**
     * @return void
     */
    public static function testProcessValueTokens2()
    {
        $givenQuery = [
            'UPDATE users SET ?a WHERE user_id = -1'
        ];
        $result = QueryHelper::processValueTokens(
            $givenQuery,
            fn($strParts) => array_map(
                fn($p) =>(in_array($p, ["?", "?d", "?#", "?a"])) ? "!" : $p
                , $strParts
            )
        );
        AssertHelper::assertArraysOfOptionScalarEquals($result, [
            'UPDATE users SET ! WHERE user_id = -1'
        ]);
    }

    /**
     * @return void
     */
    public static function runAll()
    {
        self::testProcessQuotes();
        self::testProcessCondition();
        self::testProcessValueTokens();
        self::testProcessValueTokens2();
    }
}