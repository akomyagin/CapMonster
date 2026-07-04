<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;
use JMS\Serializer\Annotation as Serializer;

final class BinanceTask extends AbstractTask
{
    public function __construct(
        string $websiteUrl,
        string $websiteKey,
        #[Serializer\SerializedName('validateId')]
        private readonly string $validateId,
        ?string $userAgent = null,
        ?string $cookies = null,
        ?ProxySetting $proxySetting = null
    ) {
        parent::__construct(TypeTask::BINANCE_TASK, $websiteUrl, $websiteKey, $userAgent, $cookies, $proxySetting);
    }

    public function getValidateId(): string
    {
        return $this->validateId;
    }
}
