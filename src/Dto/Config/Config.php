<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Config;

use JMS\Serializer\Annotation as Serializer;

final class Config
{
    /**
     * @var TimeoutConfig[]
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

    /**
     * @var string[]
     */
    #[Serializer\SerializedName(name: 'methodUrls')]
    #[Serializer\Type('array')]
    private array $methodUrls;

    #[Serializer\SerializedName(name: 'method')]
    #[Serializer\Type('string')]
    private string $method;

    public function getTimeouts(): array
    {
        return $this->timeouts;
    }

    public function getCallbackUrl(): ?string
    {
        return $this->callbackUrl;
    }

    public function getBaseUrl(): ?string
    {
        return $this->baseUrl;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function getMethodUrls(): array
    {
        return $this->methodUrls;
    }
}