<?php

namespace Tal7aouy\ApiRateLimiter;

use Laminas\Diactoros\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

class RateLimiterMiddleware implements MiddlewareInterface
{
    public function __construct(private RateLimiter $rateLimiter, private int $limit, private int $window)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $serverParams = $request->getServerParams();
        $identifier = $serverParams['REMOTE_ADDR'] ?? 'unknown';

        if ($this->rateLimiter->isAllowed($identifier, $this->limit, $this->window)) {
            return $handler->handle($request);
        }

        $response = new Response();
        $response->getBody()->write('Rate limit exceeded');
        return $response->withStatus(429);
    }
}
