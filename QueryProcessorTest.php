<?php

namespace FpDbTest;

class QueryProcessorTest
{
    private QueryProcessor $queryProcessor;

    /**
     * @param QueryProcessor $queryProcessor
     */
    public function __construct(QueryProcessor $queryProcessor)
    {
        $this->queryProcessor = $queryProcessor;
    }

    public function test1()
    {
        $givenQuery = 'SELECT name FROM users WHERE user_id = 1';
        $givenArgs = [];
        $result = $this->queryProcessor->processQuery($givenQuery, $givenArgs);
        $nominal = 'SELECT name FROM users WHERE user_id = 1';
        AssertHelper::assertOptionScalarEq($result, $nominal);
    }

    public function test2()
    {
        $givenQuery = 'SELECT * FROM users WHERE name = ? AND block = 0';
        $givenArgs = ['Jack'];
        $result = $this->queryProcessor->processQuery($givenQuery, $givenArgs);
        $nominal = 'SELECT * FROM users WHERE name = \'Jack\' AND block = 0';
        AssertHelper::assertOptionScalarEq($result, $nominal);
    }

    public function test3()
    {
        $givenQuery = 'SELECT ?# FROM users WHERE user_id = ?d AND block = ?d';
        $givenArgs = [['name', 'email'], 2, true];
        $result = $this->queryProcessor->processQuery($givenQuery, $givenArgs);
        $nominal = 'SELECT `name`, `email` FROM users WHERE user_id = 2 AND block = 1';
        AssertHelper::assertOptionScalarEq($result, $nominal);
    }

    public function test4()
    {
        $givenQuery = 'UPDATE users SET ?a WHERE user_id = -1';
        $givenArgs = [['name' => 'Jack', 'email' => null]];
        $result = $this->queryProcessor->processQuery($givenQuery, $givenArgs);
        $nominal = 'UPDATE users SET `name` = \'Jack\', `email` = NULL WHERE user_id = -1';
        AssertHelper::assertOptionScalarEq($result, $nominal);
    }

    public function test5()
    {
        $givenQuery = 'SELECT name FROM users WHERE ?# IN (?a){ AND block = ?d}';
        $givenArgs = ['user_id', [1, 2, 3], $this->queryProcessor->getSkipVal()];
        $result = $this->queryProcessor->processQuery($givenQuery, $givenArgs);
        $nominal = 'SELECT name FROM users WHERE `user_id` IN (1, 2, 3)';
        AssertHelper::assertOptionScalarEq($result, $nominal);
    }

    public function test6()
    {
        $givenQuery = 'SELECT name FROM users WHERE ?# IN (?a){ AND block = ?d}';
        $givenArgs = ['user_id', [1, 2, 3], true];
        $result = $this->queryProcessor->processQuery($givenQuery, $givenArgs);
        $nominal = 'SELECT name FROM users WHERE `user_id` IN (1, 2, 3) AND block = 1';
        AssertHelper::assertOptionScalarEq($result, $nominal);
    }

    public function runAll()
    {
        $this->test1();
        $this->test2();
        $this->test3();
        $this->test4();
        $this->test5();
        $this->test6();
    }

    public static function initDefault(): self
    {
        $mysqli = require_once __DIR__ . DIRECTORY_SEPARATOR . "mysqli.php";
        if ($mysqli->connect_errno) {
            throw new \Exception($mysqli->connect_error);
        }
        return new self(
            new QueryProcessor(
                new MysqlDbFormatter(
                    $mysqli
                )
            )
        );
    }
}