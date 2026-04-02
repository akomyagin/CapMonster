<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;

final class BasiliskTask extends AbstractTask
{
    public function __construct(
        string $websiteUrl,
        string $websiteKey,
        private readonly ?string $metadata = null,
        ?string $userAgent = null,
        ?string $cookies = null,
        ?ProxySetting $proxySetting = null
    ) {
        parent::__construct(TypeTask::BASILISK_TASK, $websiteUrl, $websiteKey, $userAgent, $cookies, $proxySetting);
    }

    public function getMetadata(): ?string
    {
        return $this->metadata;
    }
}
