<?php
namespace FpDbTest;

class TokenizationRequest
{
    private string $query;
    private array $vals;

    /**
     * @param string $query
     * @param array $vals
     */
    public function __construct(string $query, array $vals)
    {
        $this->query = $query;
        $this->vals = array_values($vals);
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @return array
     */
    public function getVals(): array
    {
        return $this->vals;
    }

}