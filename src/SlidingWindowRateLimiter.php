<?php

namespace Tal7aouy\ApiRateLimiter;

class SlidingWindowRateLimiter implements RateLimiter
{
    private array $requests = [];

    public function isAllowed(string $identifier, int $limit, int $window): bool
    {
        $currentTime = time();
        if (!isset($this->requests[$identifier])) {
            $this->requests[$identifier] = [];
        }

        // Remove requests that are outside the window
        $this->requests[$identifier] = array_filter($this->requests[$identifier], fn($timestamp): bool => ($timestamp + $window) > $currentTime);

        // Check if the request count is within the limit
        if (count($this->requests[$identifier]) < $limit) {
            $this->requests[$identifier][] = $currentTime;
            return true;
        }

        return false;
    }
}
