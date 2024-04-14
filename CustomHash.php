<?php
namespace FpDbTest;

/**
 * Custom hash value-object
 */
class CustomHash
{
    private string $hash = '';

    public function __construct(int $len, string $excludeChars = "")
    {
        $excludeSplit = str_split($excludeChars);
        $seed = array_filter(
            str_split('abcdefghijklmnopqrstuvwxyz'
                .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                .'0123456789!@#$%^&*()?№:^;'),
            fn($ch) => !in_array($ch, $excludeSplit)
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

    /**
     * @param string $v
     * @return bool
     */
    public function isHash(string $v): bool
    {
        return $v === $this->hash;
    }

}