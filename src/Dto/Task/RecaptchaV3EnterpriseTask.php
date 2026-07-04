<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;
use JMS\Serializer\Annotation as Serializer;

final class RecaptchaV3EnterpriseTask extends AbstractTask
{
    public function __construct(
        string $websiteUrl,
        string $websiteKey,
        #[Serializer\SerializedName('minScore')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?float $minScore = null,
        #[Serializer\SerializedName('pageAction')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?string $pageAction = null,
        #[Serializer\SerializedName('enterprisePayload')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?string $enterprisePayload = null,
        #[Serializer\SerializedName('apiDomain')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?string $apiDomain = null,
        ?string $userAgent = null,
        ?string $cookies = null,
        ?ProxySetting $proxySetting = null
    ) {
        parent::__construct(
            TypeTask::RECAPTCHA_V3_ENTERPRISE_TASK,
            $websiteUrl,
            $websiteKey,
            $userAgent,
            $cookies,
            $proxySetting
        );
    }

    public function getMinScore(): ?float
    {
        return $this->minScore;
    }

    public function getPageAction(): ?string
    {
        return $this->pageAction;
    }

    public function getEnterprisePayload(): ?string
    {
        return $this->enterprisePayload;
    }

    public function getApiDomain(): ?string
    {
        return $this->apiDomain;
    }
}
