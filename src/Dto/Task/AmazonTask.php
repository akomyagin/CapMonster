<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;

final class AmazonTask extends AbstractTask
{
    public function __construct(
        string $websiteUrl,
        string $websiteKey,
        private readonly ?string $challengeScript = null,
        ?string $userAgent = null,
        ?string $cookies = null,
        ?ProxySetting $proxySetting = null
    ) {
        parent::__construct(TypeTask::AMAZON_TASK, $websiteUrl, $websiteKey, $userAgent, $cookies, $proxySetting);
    }

    public function getChallengeScript(): ?string
    {
        return $this->challengeScript;
    }
}
