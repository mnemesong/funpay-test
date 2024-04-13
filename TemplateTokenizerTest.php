<?php
namespace FpDbTest;

/**
 * Тест-кейс для проверки TemplateTokenizer
 */
class TemplateTokenizerTest
{
    /**
     * @return void
     * @throws \Exception
     */
    public static function test1()
    {
        $mysqli = require_once __DIR__ . DIRECTORY_SEPARATOR . "mysqli.php";
        if ($mysqli->connect_errno) {
            throw new \Exception($mysqli->connect_error);
        }
        $tokenizer = new TemplateTokenizer(new MysqlDbFormatter($mysqli));
        $givenQuery = 'SELECT ?# FROM users WHERE user_id = ?d AND block = ?';
        $givenParams = [['name', 'email'], 2, true];
        $result = $tokenizer->formatAndInjectValues($givenQuery, $givenParams);
        $nominal = 'SELECT `name`, `email` FROM users WHERE user_id = 2 AND block = TRUE';
        Asserter::assertOptionScalarEq($result, $nominal);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public static function runAll()
    {
        self::test1();
    }
}