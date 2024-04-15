<?php
namespace FpDbTest;

class StringHelperTest
{
    public static function testTokenize1()
    {
        $givenPattern = '/\?[dfa\#]?/';
        $givenQuery = 'SELECT ?# FROM users WHERE user_id = ?d AND block = ??';
        $result = StringHelper::tokenize($givenQuery, $givenPattern);
        AssertHelper::assertArraysOfOptionScalarEquals($result, [
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

    public static function testTokenize2()
    {
        $givenPattern = "/" . implode("|",array_map(
                fn($ch) => "(${ch}(?:(?:[^${ch}])|(?:\\\\${ch}))*[^\\\\]${ch})",
                ['"', "'", "`"]
            )) . "/";;
        $givenQuery = 'SELECT ?# FROM `users` WHERE user_id = ?d AND block = "data"';
        $result = StringHelper::tokenize($givenQuery, $givenPattern);
        AssertHelper::assertArraysOfOptionScalarEquals($result, [
            'SELECT ?# FROM ',
            '`users`',
            ' WHERE user_id = ?d AND block = ',
            '"data"',
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
                AssertHelper::assertArraysOfOptionScalarEquals($newParts, [
                     "ce1c", "c412 ", " 4c12", "4c21", "412c421", ""
                ]);
                return ["1", "2", "3", "4", "5", "6"];
            }
        );
        AssertHelper::assertArraysOfOptionScalarEquals($result, [
            "1", "2?:3", "4?:5", "6"
        ]);
    }

    public static function runAll()
    {
        self::testTokenize1();
        self::testTokenize2();
        self::testResplit();
    }
}