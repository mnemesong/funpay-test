<?php
namespace FpDbTest;

class StringHelperTest
{
    public static function test1()
    {
        $givenPattern = '/\?[dfa\#]?/';
        $givenQuery = 'SELECT ?# FROM users WHERE user_id = ?d AND block = ??';
        $result = StringHelper::tokenize($givenQuery, $givenPattern);
        Asserter::assertArraysOfOptionScalarEquals($result, [
            'SELECT ',
            '?#',
            ' FROM users WHERE user_id = ',
            '?d',
            ' AND block = ',
            '?',
            '',
            '?',
            ''
        ]);
    }

    public static function runAll()
    {
        self::test1();
    }
}