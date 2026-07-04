<?php

declare(strict_types=1);

namespace Tests\Support;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

/**
 * PSR-15 handler for webclient/fake-http-client: returns queued responses in FIFO order
 * and records every request (method, URI, headers, raw body) it received.
 */
final class RecordingHandler implements RequestHandlerInterface
{
    /** @var list<ResponseInterface> */
    private array $queue = [];

    /** @var list<array{method: string, uri: string, headers: array<string, list<string>>, body: string}> */
    private array $requests = [];

    public function push(ResponseInterface $response): self
    {
        $this->queue[] = $response;

        return $this;
    }

    /**
     * @param array<string, mixed>|string $payload raw JSON string or an array to encode
     */
    public function pushJson(array|string $payload, int $status = 200): self
    {
        $body = is_string($payload) ? $payload : json_encode($payload, JSON_THROW_ON_ERROR);

        return $this->push(new Response($status, ['Content-Type' => 'application/json'], $body));
    }

    public function pushText(string $body, int $status = 200): self
    {
        return $this->push(new Response($status, ['Content-Type' => 'text/plain'], $body));
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->requests[] = [
            'method' => $request->getMethod(),
            'uri' => (string) $request->getUri(),
            'headers' => $request->getHeaders(),
            'body' => (string) $request->getBody(),
        ];
        if ($this->queue === []) {
            throw new RuntimeException('RecordingHandler: no queued response left for ' . $request->getUri());
        }

        return array_shift($this->queue);
    }

    /**
     * @return list<array{method: string, uri: string, headers: array<string, list<string>>, body: string}>
     */
    public function requests(): array
    {
        return $this->requests;
    }

    /**
     * @return array{method: string, uri: string, headers: array<string, list<string>>, body: string}
     */
    public function request(int $index): array
    {
        if (!isset($this->requests[$index])) {
            throw new RuntimeException(sprintf('RecordingHandler: no request recorded at index %d', $index));
        }

        return $this->requests[$index];
    }

    public function requestCount(): int
    {
        return count($this->requests);
    }
}
