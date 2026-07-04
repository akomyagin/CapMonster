<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Config;

use JMS\Serializer\Annotation as Serializer;

final class Config
{
    /**
     * @var list<TimeoutConfig>
     */
    #[Serializer\SerializedName(name: 'timeouts')]
    #[Serializer\Type("array<CapMonsterClient\Dto\Config\TimeoutConfig>")]
    private readonly array $timeouts;

    #[Serializer\SerializedName(name: 'callbackUrl')]
    #[Serializer\Type('string')]
    private ?string $callbackUrl = null;

    #[Serializer\SerializedName(name: 'baseUrl')]
    #[Serializer\Type('string')]
    private string $baseUrl;

    #[Serializer\SerializedName(name: 'method')]
    #[Serializer\Type('string')]
    private string $method;

    #[Serializer\SerializedName(name: 'maxGetTaskResultAttempts')]
    #[Serializer\Type('int')]
    private int $maxGetTaskResultAttempts;

    /**
     * @var array<string, string|string[]>
     */
    #[Serializer\SerializedName(name: 'headers')]
    #[Serializer\Type('array')]
    private array $headers = [];

    /**
     * @return list<TimeoutConfig>
     */
    public function getTimeouts(): array
    {
        return $this->timeouts;
    }

    public function getCallbackUrl(): ?string
    {
        return $this->callbackUrl;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getMaxGetTaskResultAttempts(): int
    {
        return $this->maxGetTaskResultAttempts;
    }

    /**
     * @return array<string, string|string[]>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}