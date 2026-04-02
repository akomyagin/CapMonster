<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;

final class RecaptchaV3EnterpriseTask extends AbstractTask
{
    public function __construct(
        string $websiteUrl,
        string $websiteKey,
        private readonly ?float $minScore = null,
        private readonly ?string $pageAction = null,
        private readonly ?string $enterprisePayload = null,
        private readonly ?string $apiDomain = null,
        ?string $userAgent = null,
        ?string $cookies = null,
        ?ProxySetting $proxySetting = null
    ) {
        parent::__construct(
            ($proxySetting === null) ? TypeTask::RECAPTCHA_V3_ENTERPRISE_TASK_PROXYLESS : TypeTask::RECAPTCHA_V3_ENTERPRISE_TASK,
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
