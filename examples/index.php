<?php

require __DIR__ . '/../vendor/autoload.php';

use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tal7aouy\ApiRateLimiter\RedisConnection;
use Tal7aouy\ApiRateLimiter\RedisRateLimiter;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Tal7aouy\ApiRateLimiter\RateLimiterMiddleware;

// Simple Request Handler
class SimpleHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = new Response();
        $response->getBody()->write('Hello, World!');
        return $response;
    }
}

// Initialize Redis Rate Limiter
$redisConnection = new RedisConnection();
$redisRateLimiter = new RedisRateLimiter($redisConnection->getClient());

// Initialize Rate Limiter Middleware
$rateLimiterMiddleware = new RateLimiterMiddleware($redisRateLimiter, 2, 60); // Limit: 10 requests per minute

// Create Server Request
$request = ServerRequestFactory::fromGlobals();

// Process Request through Middleware
$response = $rateLimiterMiddleware->process($request, new SimpleHandler());

// Emit Response
$emitter = new SapiEmitter();
$emitter->emit($response);
