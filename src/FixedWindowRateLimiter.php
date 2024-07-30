<?php

declare(strict_types=1);

namespace Tal7aouy\ApiRateLimiter;


class FixedWindowRateLimiter implements RateLimiter
{
    private array $requests = [];

    public function isAllowed(string $identifier, int $limit, int $window): bool
    {
        $currentTime = time();
        $windowStart = (int)($currentTime / $window) * $window;

        if (!isset($this->requests[$identifier])) {
            $this->requests[$identifier] = [];
        }

        // Reset count for a new window
        if (!isset($this->requests[$identifier][$windowStart])) {
            $this->requests[$identifier] = [$windowStart => 0];
        }

        if ($this->requests[$identifier][$windowStart] < $limit) {
            $this->requests[$identifier][$windowStart]++;
            return true;
        }

        return false;
    }
}
