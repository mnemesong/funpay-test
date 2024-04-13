<?php
namespace FpDbTest;

class TokenizationResult
{
    /**
     * Parts of tokenized string splitted by all tokens
     * @var array
     */
    private array $strParts;

    /**
     * Associative array kind of [positionInt => tokenStr]
     * @var string[]
     */
    private array $tokens = [];

    private string $pattern;

    private string $primalStr;

    /**
     * @param string $str
     * @param string $pattern
     */
    public function __construct(string $str, string $pattern)
    {
        $this->primalStr = $str;
        $this->pattern = $pattern;
        $matches = [];
        preg_match_all(
            $pattern,
            $str,
            $matches,
            PREG_OFFSET_CAPTURE
        );
        if(!empty($matches) && !empty($matches[0])) {
            $result = $matches[0]; /* [tokenAsString, positionAsInt][] */
            $this->tokens = array_combine(
                array_map(fn(array $v) => $v[1], $result),
                array_map(fn(array $v) => $v[0], $result)
            );
        }
        $pregSplit = preg_split($pattern, $str);
        Asserter::assertOk($pregSplit !== false, "Invalid preg_split "
            . "'${str}' by pattern ${pattern}" );
        $this->strParts = $pregSplit;
    }

    /**
     * @return array
     */
    public function getStrParts()
    {
        return $this->strParts;
    }

    /**
     * @return string[]
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @return string
     */
    public function getPrimalStr(): string
    {
        return $this->primalStr;
    }

}