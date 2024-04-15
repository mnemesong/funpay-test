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
        Asserter::assertArraysOfOptionScalarEquals( $result, [
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
        Asserter::assertArraysOfOptionScalarEquals($result, [
            '!SELECT ?# FROM `users` ',
            '!WHERE user_id = ?d {! AND block = "dsaa" }!'
        ]);
    }

    /**
     * @return void
     */
    public static function runAll()
    {
        self::testProcessQuotes();
        self::testProcessCondition();
    }
}