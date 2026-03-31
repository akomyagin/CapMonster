<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;

abstract class AbstractTask
{
    public function __construct(
        private readonly TypeTask $type,
        private readonly string $websiteUrl,
        private readonly string $websiteKey,
        private readonly ?string $userAgent = null,
        private readonly ?string $cookies = null,
        private readonly ?ProxySetting $proxySetting = null,
        private int $taskId = 0
    ) {
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