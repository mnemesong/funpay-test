<?php

namespace FpDbTest;

use mysqli;

/**
 * Реализация БД на Mysql
 */
class Database implements DatabaseInterface
{
    private mysqli $mysqli;
    private QueryProcessor $queryProcessor;

    /**
     * @param mysqli $mysqli
     */
    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
        $this->queryProcessor = new QueryProcessor(
            new MysqlDbFormatter($this->mysqli)
        );
    }

    /**
     * Сформатировать sql-запрос
     * @param string $query
     * @param array $args
     * @return string
     */
    public function buildQuery(string $query, array $args = []): string
    {
        return $this->queryProcessor->processQuery($query, $args);
    }

    /**
     * Возвращает SKIP-значение
     * @return string
     */
    public function skip()
    {
        return $this->queryProcessor->getSkipVal();
    }
}
