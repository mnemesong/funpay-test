<?php
namespace FpDbTest;

/**
 * Настраиваемый хэш
 */
class CustomHash
{
    private string $hash = '';

    /**
     * @param int $len
     * @param string $excludeChars
     */
    public function __construct(int $len, string $excludeChars = "")
    {
        $excludeSplit = mb_str_split($excludeChars);
        $seed = array_filter(
            mb_str_split('abcdefghijklmnopqrstuvwxyz'
                .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                .'0123456789!@#$%^&*()?:^;'),
            fn($ch) => !in_array(mb_strtolower($ch), $excludeSplit)
                && !in_array(mb_strtoupper($ch), $excludeSplit)
        );
        shuffle($seed);
        foreach (array_rand($seed, $len) as $k) {
            $this->hash .= $seed[$k];
        }
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

}