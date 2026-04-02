<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;

final class DataDomeTask extends AbstractTask
{
    public function __construct(
        string $websiteUrl,
        string $websiteKey,
        private readonly ?string $captchaUrl = null,
        private readonly ?string $metadata = null,
        ?string $userAgent = null,
        ?string $cookies = null,
        ?ProxySetting $proxySetting = null
    ) {
        parent::__construct(
            TypeTask::DATADOME_TASK,
            $websiteUrl,
            $websiteKey,
            $userAgent,
            $cookies,
            $proxySetting
        );
    }

    public function getCaptchaUrl(): ?string
    {
        return $this->captchaUrl;
    }

    public function getMetadata(): ?string
    {
        return $this->metadata;
    }
}
