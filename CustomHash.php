<?php
namespace FpDbTest;

/**
 * Custom hash value-object
 */
class CustomHash
{
    private string $hash = 'c412*-L';

    public function __construct()
    {
        $seed = str_split('abcdefghijklmnopqrstuvwxyz'
            .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
            .'0123456789!@#$%^&*()');
        shuffle($seed);
        foreach (array_rand($seed, 22) as $k) {
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