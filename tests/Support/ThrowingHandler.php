<?php

declare(strict_types=1);

namespace Tests\Support;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

/**
 * PSR-15 handler that always fails. FakeHttpClient wraps the throwable into a
 * PSR-18 NetworkError (ClientExceptionInterface), which simulates a transport failure.
 */
final class ThrowingHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        throw new RuntimeException('simulated network failure');
    }
}
