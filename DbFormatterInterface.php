<?php
namespace FpDbTest;

/**
 * Контракт класса, форматирующего зачения для конкретной БД
 */
interface DbFormatterInterface
{
    /**
     * @param scalar|null $val
     * @return string
     */
    public function formatOptionScalarVal($val): string;
}