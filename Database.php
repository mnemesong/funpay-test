<?php

namespace FpDbTest;

use Exception;
use mysqli;
use FpDbTest\TokenizationResult;

class Database implements DatabaseInterface
{
    private mysqli $mysqli;
    private CustomHash $skipHash;

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
        $this->skipHash = new CustomHash(20);
    }

    public function buildQuery(string $query, array $args = []): string
    {
        throw new Exception();
    }

    public function skip()
    {
        return $this->skipHash->getHash();
    }
}
