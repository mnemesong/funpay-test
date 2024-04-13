<?php
namespace FpDbTest;

class QuotesTokenizer implements TokenizerInterface
{
    private string $valuesTokenPattern;

    /**
     * @param string $valuesTokenPattern
     */
    public function __construct(string $valuesTokenPattern)
    {
        $this->valuesTokenPattern = $valuesTokenPattern;
    }

    /**
     * @param string $query
     * @return TokenizationResult
     */
    public function explode(string $query): TokenizationResult {
        $pattern = "/" . implode("|",array_map(
            fn($ch) => "(${ch}(?:(?:[^${ch}])|(?:\\\\${ch}))*[^\\\\]${ch})",
            ['"', "'", "`"]
        )) . "/";
        return new TokenizationResult($query, $pattern);
    }

    /**
     * @param TokenizationRequest $request
     * @return TokenizationRequest[]
     */
    public function tokenize(TokenizationRequest $request): array
    {
        $tokenResult = $this->explode($request->getQuery());
        $queryPartRequests = $this->distributeValuesByQueryParts(
            $tokenResult->getStrParts(),
            $request->getVals()
        );
        $quottedPartRequests = array_map(
            fn($s) => new TokenizationRequest($s, []),
            $tokenResult->getTokens()
        ); //TokenizationRequest[]
        return ArrayHelper::mix($queryPartRequests, $quottedPartRequests);
    }

    /**
     * @param string[] $queryParts
     * @param array $vals
     * @return TokenizationRequest[]
     */
    private function distributeValuesByQueryParts(
        array $queryParts,
        array $vals
    ): array {
        $tokenizedQueryParts = array_map(
            fn(string $s) => new TokenizationResult($s, $this->valuesTokenPattern),
            $queryParts
        );
        $partsCnt = array_reduce(
            $tokenizedQueryParts,
            fn($acc, TokenizationResult $el) => $acc + count($el->getTokens()),
            0
        );
        Asserter::assertOk($partsCnt === count($vals),
            "after tokenization count of tokens in query strings "
            . "should be equal count of values");

        //Grouping values by token-consistent query strings
        $queryPartReducingResult = array_reduce(
            $tokenizedQueryParts,
            fn($acc, TokenizationResult $el) => [
                array_merge($acc[0], [
                    new TokenizationRequest(
                        $el->getPrimalStr(),
                        array_slice($acc[1], 0, count($el->getTokens()))
                    ),
                ]),
                array_slice($acc[1], count($el->getTokens()))
            ],
            [[], $vals]
        );
        Asserter::assertOk(count($queryPartReducingResult[1]) === 0,
            "after distributing values by token-consistent query strings "
            . "should not stay exists extra not-distributed values");

        return $queryPartReducingResult[0];
    }
}