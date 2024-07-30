<?php

namespace Tal7aouy\ApiRateLimiter;


use Predis\Client;

class RedisConnection
{
    private \Predis\Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
