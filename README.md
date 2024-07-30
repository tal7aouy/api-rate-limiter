# API Rate Limiting Library

This library provides rate limiting functionality for PHP applications, using Redis for storage. It supports multiple rate limiting strategies and can be integrated with PSR-15 middleware.

## Installation

To use the library, you need to have the following Composer packages, which are already included in the library's `composer.json` file:

1. **`predis/predis`** - A Redis client library.
2. **`laminas/laminas-diactoros`** - Provides PSR-7 (HTTP message) and PSR-15 (HTTP server) implementations.
3. **`psr/http-server-handler`** - Provides the `RequestHandlerInterface` for handling HTTP requests.
4. **`psr/http-server-middleware`** - Provides the `MiddlewareInterface` for HTTP middleware.
5. **`laminas/laminas-httphandlerrunner`** - Provides the `SapiEmitter` class for emitting HTTP responses.

In addition, for development and testing, the library includes:

1. **`phpstan/phpstan`** - For static analysis.
2. **`pestphp/pest`** - For testing.

### Install Library and Dependencies

Run the following Composer command to install all required packages:

```bash
composer require tal7aouy/api-rate-limiter
```

Ensure Redis is installed and running on your system.

## Usage

### Basic Setup

1. **Initialize Redis Connection:**

    Create a `RedisConnection` instance to handle the connection to the Redis server.

    ```php
    use Tal7aouy\ApiRateLimiter\RedisConnection;

    $redisConnection = new RedisConnection();
    ```

2. **Initialize Redis Rate Limiter:**

    Create a `RedisRateLimiter` instance using the Redis connection.

    ```php
    use Tal7aouy\ApiRateLimiter\RedisRateLimiter;

    $redisRateLimiter = new RedisRateLimiter($redisConnection->getClient());
    ```

3. **Initialize Rate Limiter Middleware:**

    Create a `RateLimiterMiddleware` instance with the desired rate limit and time window.

    ```php
    use Tal7aouy\ApiRateLimiter\RateLimiterMiddleware;

    $rateLimiterMiddleware = new RateLimiterMiddleware($redisRateLimiter, 10, 60); // Limit: 10 requests per minute
    ```

4. **Create a Request Handler:**

    Implement a simple request handler to handle incoming requests.

    ```php
    use Laminas\Diactoros\Response;
    use Psr\Http\Server\RequestHandlerInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Psr\Http\Message\ResponseInterface;

    class SimpleHandler implements RequestHandlerInterface {
        public function handle(ServerRequestInterface $request): ResponseInterface {
            $response = new Response();
            $response->getBody()->write('Hello, World!');
            return $response;
        }
    }
    ```

5. **Create Server Request and Process Through Middleware:**

    Create a server request and process it through the rate limiter middleware.

    ```php
    use Laminas\Diactoros\ServerRequestFactory;

    $serverParams = ['REMOTE_ADDR' => '127.0.0.1'];
    $request = ServerRequestFactory::fromGlobals($serverParams, [], [], [], []);

    $response = $rateLimiterMiddleware->process($request, new SimpleHandler());

    // Emit Response
    use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
    $emitter = new SapiEmitter();
    $emitter->emit($response);
    ```

### Advanced Configuration

#### Custom Redis Configuration

You can configure the Redis connection by passing a custom configuration array to the `RedisConnection` class.

```php
$redisConnection = new RedisConnection([
    'scheme' => 'tcp',
    'host'   => 'localhost',
    'port'   => 6379,
]);
```

#### Custom Rate Limiting Strategies

The library supports multiple rate limiting strategies. You can implement your own strategy by extending the `RateLimiter` interface.

```php
use Tal7aouy\ApiRateLimiter\RateLimiter;

class CustomRateLimiter implements RateLimiter {
    public function isAllowed(string $identifier, int $limit, int $window): bool {
        // Custom rate limiting logic
    }
}
```

Then use the custom rate limiter with the middleware.

```php
$customRateLimiter = new CustomRateLimiter();
$rateLimiterMiddleware = new RateLimiterMiddleware($customRateLimiter, 10, 60);
```

### Testing

Unit tests are provided for each rate limiting strategy and Redis integration. To run the tests, use PestPHP.

```bash
composer test
```

### Documentation

For detailed documentation and examples, please refer to the source code and comments within the library.

In the `examples/index.php` file, you can see a complete example of how to use the library.

## Contributing

Contributions are welcome!
Please fork the repository and submit a pull request.

## License

This software licensed under the MIT license.
