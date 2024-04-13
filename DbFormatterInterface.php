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

    /**
     * @param string $val
     * @return string
     */
    public function formatFieldName(string $val): string;

    /**
     * @param scalar[]|null[] $val
     * @return string
     */
    public function formatListOfVals(array $val): string;

    /**
     * @param scalar[]|null[] $val
     * @return string
     */
    public function formatAssociativeArrayOfVals(array $val): string;

    /**
     * @param string[] $val
     * @return string
     */
    public function formatListOfFields(array $val): string;

    /**
     * @param string[] $val
     * @return string
     */
    public function formatAssociativeArrayOfFields(array $val): string;
}