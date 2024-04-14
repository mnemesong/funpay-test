<?php
namespace FpDbTest;

class QueryHelperTest
{
    public static function testProcessQuotes()
    {
        $givenQuery = [
            'SELECT ?# FROM `users` ',
            'WHERE user_id = ?d AND block = "dsaa"'
        ];
        $result = \FpDbTest\QueryHelper::processQuotes(
            $givenQuery,
            fn($strParts) => array_map(fn($p) => "!" . $p, $strParts)
        );
        \FpDbTest\Asserter::assertArraysOfOptionScalarEquals( $result, [
            '!SELECT ?# FROM `users`! ',
            '!WHERE user_id = ?d AND block = "dsaa"!'
        ]);
    }

    public static function runAll()
    {
        self::testProcessQuotes();
    }
}