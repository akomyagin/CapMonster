<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;
use JMS\Serializer\Annotation as Serializer;

final class TurnstileChallengeTask extends AbstractTask
{
    /**
     * @param string $cloudflareTaskType 'token' | 'cf_clearance'
     */
    public function __construct(
        string $websiteUrl,
        string $websiteKey,
        #[Serializer\SerializedName('cloudflareTaskType')]
        private readonly string $cloudflareTaskType,
        #[Serializer\SerializedName('pageAction')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?string $pageAction = null,
        #[Serializer\SerializedName('pageData')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?string $pageData = null,
        #[Serializer\SerializedName('data')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?string $data = null,
        #[Serializer\SerializedName('htmlPageBase64')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?string $htmlPageBase64 = null,
        ?string $userAgent = null,
        ?ProxySetting $proxySetting = null
    ) {
        parent::__construct(
            TypeTask::TURNSTILE_CHALLENGE_TASK,
            $websiteUrl,
            $websiteKey,
            $userAgent,
            proxySetting: $proxySetting
        );
    }

    public function getCloudflareTaskType(): string
    {
        return $this->cloudflareTaskType;
    }

    public function getPageAction(): ?string
    {
        return $this->pageAction;
    }

    public function getPageData(): ?string
    {
        return $this->pageData;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function getHtmlPageBase64(): ?string
    {
        return $this->htmlPageBase64;
    }
}
