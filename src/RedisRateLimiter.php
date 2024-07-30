<?php

namespace Tal7aouy\ApiRateLimiter;

use Predis\Client;

class RedisRateLimiter implements RateLimiter
{
    public function __construct(private Client $client)
    {
    }

    public function isAllowed(string $identifier, int $limit, int $window): bool
    {
        $currentTime = time();
        $key = "rate_limiter:{$identifier}";

        $luaScript = "
            local key = KEYS[1]
            local currentTime = tonumber(ARGV[1])
            local window = tonumber(ARGV[2])
            local limit = tonumber(ARGV[3])

            redis.call('zremrangebyscore', key, '-inf', currentTime - window)
            local requestCount = redis.call('zcard', key)

            if requestCount < limit then
                redis.call('zadd', key, currentTime, currentTime)
                redis.call('expire', key, window)
                return 1
            else
                return 0
            end
        ";

        $result = $this->client->eval($luaScript, 1, $key, $currentTime, $window, $limit);
        return $result == 1;
    }
}
