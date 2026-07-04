<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;

final class ProsopoTask extends AbstractTask
{
    public function __construct(
        string $websiteUrl,
        string $websiteKey,
        ?string $userAgent = null,
        ?string $cookies = null,
        ?ProxySetting $proxySetting = null
    ) {
        parent::__construct(TypeTask::PROSOPO_TASK, $websiteUrl, $websiteKey, $userAgent, $cookies, $proxySetting);
    }
}
