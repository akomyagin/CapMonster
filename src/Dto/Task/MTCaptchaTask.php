<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;
use JMS\Serializer\Annotation as Serializer;

final class MTCaptchaTask extends AbstractTask
{
    public function __construct(
        string $websiteUrl,
        string $websiteKey,
        #[Serializer\SerializedName('pageAction')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?string $pageAction = null,
        #[Serializer\SerializedName('isInvisible')]
        #[Serializer\Type('boolean')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?bool $isInvisible = null,
        ?string $userAgent = null,
        ?string $cookies = null,
        ?ProxySetting $proxySetting = null
    ) {
        parent::__construct(TypeTask::MT_CAPTCHA_TASK, $websiteUrl, $websiteKey, $userAgent, $cookies, $proxySetting);
    }

    public function getPageAction(): ?string
    {
        return $this->pageAction;
    }

    public function getIsInvisible(): ?bool
    {
        return $this->isInvisible;
    }
}
