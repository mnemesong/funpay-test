<?php
namespace FpDbTest;

interface TokenizerInterface
{
    /**
     * @param TokenizationRequest $request
     * @return TokenizationRequest[]
     */
    public function tokenize(TokenizationRequest $request): array;
}