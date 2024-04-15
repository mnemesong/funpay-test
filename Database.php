<?php

namespace FpDbTest;

use mysqli;

class Database implements DatabaseInterface
{
    private mysqli $mysqli;
    private QueryProcessor $queryProcessor;

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
        $this->queryProcessor = new QueryProcessor(
            new MysqlDbFormatter($this->mysqli)
        );
    }

    public function buildQuery(string $query, array $args = []): string
    {
        echo "Building query: " . $query . "\n";
        return $this->queryProcessor->processQuery($query, $args);
    }

    public function skip()
    {
        return $this->queryProcessor->getSkipVal();
    }
}
