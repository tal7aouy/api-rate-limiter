<?php

namespace Tal7aouy\ApiRateLimiter;

interface RateLimiter
{
    public function isAllowed(string $identifier, int $limit, int $window): bool;
}
