<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;
use JMS\Serializer\Annotation as Serializer;

final class TurnstileTask extends AbstractTask
{
    public function __construct(
        string $websiteUrl,
        string $websiteKey,
        #[Serializer\SerializedName('pageAction')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?string $pageAction = null,
        #[Serializer\SerializedName('data')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?string $data = null,
        ?string $userAgent = null,
        ?ProxySetting $proxySetting = null
    ) {
        parent::__construct(
            TypeTask::TURNSTILE_TASK,
            $websiteUrl,
            $websiteKey,
            $userAgent,
            proxySetting: $proxySetting
        );
    }

    public function getPageAction(): ?string
    {
        return $this->pageAction;
    }

    public function getData(): ?string
    {
        return $this->data;
    }
}
