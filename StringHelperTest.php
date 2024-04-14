<?php
namespace FpDbTest;

class StringHelperTest
{
    public static function testTokenize()
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

    public static function testResplit()
    {
        $givenStrParts = ["ce1c", "c412 ?: 4c12", "4c21?:412c421", ""];
        $result = StringHelper::resplit(
            $givenStrParts,
            "?:",
            function ($newParts) {
                Asserter::assertArraysOfOptionScalarEquals($newParts, [
                     "ce1c", "c412 ", " 4c12", "4c21", "412c421", ""
                ]);
                return ["1", "2", "3", "4", "5", "6"];
            }
        );
        Asserter::assertArraysOfOptionScalarEquals($result, [
            "1", "2?:3", "4?:5", "6"
        ]);
    }

    public static function runAll()
    {
        self::testTokenize();
        self::testResplit();
    }
}