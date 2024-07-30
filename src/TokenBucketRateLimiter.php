<?php

namespace Tal7aouy\ApiRateLimiter;

class TokenBucketRateLimiter implements RateLimiter
{
    private array $tokens = [];
    private array $lastRefillTime = [];

    public function isAllowed(string $identifier, int $limit, int $window): bool
    {
        $currentTime = time();
        $rate = $limit / $window;

        if (!isset($this->tokens[$identifier])) {
            $this->tokens[$identifier] = $limit;
            $this->lastRefillTime[$identifier] = $currentTime;
        }

        // Calculate how many tokens to add since the last request
        $elapsedTime = $currentTime - $this->lastRefillTime[$identifier];
        $tokensToAdd = $elapsedTime * $rate;
        $this->tokens[$identifier] = min($limit, $this->tokens[$identifier] + $tokensToAdd);
        $this->lastRefillTime[$identifier] = $currentTime;

        if ($this->tokens[$identifier] >= 1) {
            $this->tokens[$identifier]--;
            return true;
        }

        return false;
    }
}
