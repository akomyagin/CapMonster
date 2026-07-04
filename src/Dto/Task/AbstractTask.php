<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;
use JMS\Serializer\Annotation as Serializer;

abstract class AbstractTask
{
    public function __construct(
        #[Serializer\Exclude]
        private readonly TypeTask $type,
        #[Serializer\SerializedName('websiteURL')]
        private readonly string $websiteUrl,
        #[Serializer\SerializedName('websiteKey')]
        private readonly string $websiteKey,
        #[Serializer\SerializedName('userAgent')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?string $userAgent = null,
        #[Serializer\SerializedName('cookies')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?string $cookies = null,
        #[Serializer\Exclude]
        private readonly ?ProxySetting $proxySetting = null,
        #[Serializer\Exclude]
        private int $taskId = 0
    ) {
    }

    #[Serializer\VirtualProperty(name: 'type')]
    #[Serializer\SerializedName('type')]
    public function jmsSerializeTaskType(): string
    {
        return $this->type->value;
    }

    #[Serializer\VirtualProperty(name: 'proxyType')]
    #[Serializer\SerializedName('proxyType')]
    #[Serializer\SkipWhenEmpty()]
    public function jmsSerializeProxyType(): ?string
    {
        return $this->proxySetting?->getProxyType();
    }

    #[Serializer\VirtualProperty(name: 'proxyAddress')]
    #[Serializer\SerializedName('proxyAddress')]
    #[Serializer\SkipWhenEmpty()]
    public function jmsSerializeProxyAddress(): ?string
    {
        return $this->proxySetting?->getProxyAddress();
    }

    #[Serializer\VirtualProperty(name: 'proxyPort')]
    #[Serializer\SerializedName('proxyPort')]
    #[Serializer\SkipWhenEmpty()]
    public function jmsSerializeProxyPort(): ?int
    {
        return $this->proxySetting?->getProxyPort();
    }

    #[Serializer\VirtualProperty(name: 'proxyLogin')]
    #[Serializer\SerializedName('proxyLogin')]
    #[Serializer\SkipWhenEmpty()]
    public function jmsSerializeProxyLogin(): ?string
    {
        return $this->proxySetting?->getProxyLogin();
    }

    #[Serializer\VirtualProperty(name: 'proxyPassword')]
    #[Serializer\SerializedName('proxyPassword')]
    #[Serializer\SkipWhenEmpty()]
    public function jmsSerializeProxyPassword(): ?string
    {
        return $this->proxySetting?->getProxyPassword();
    }

    public function getType(): TypeTask
    {
        return $this->type;
    }

    public function getProxySetting(): ?ProxySetting
    {
        return $this->proxySetting;
    }

    public function getWebsiteUrl(): string
    {
        return $this->websiteUrl;
    }

    public function getWebsiteKey(): string
    {
        return $this->websiteKey;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function getCookies(): ?string
    {
        return $this->cookies;
    }

    public function getTaskId(): int
    {
        return $this->taskId;
    }

    public function setTaskId(int $taskId): self
    {
        $this->taskId = $taskId;

        return $this;
    }
}