<?php

declare(strict_types = 1);

namespace CapMonsterClient\Dto\Config;

use CapMonsterClient\Enum\TypeTask;
use JMS\Serializer\Annotation as Serializer;

final class TimeoutConfig
{
    public function __construct(
        #[Serializer\SerializedName(name: 'taskType')]
        #[Serializer\Type('enum<"CapMonsterClient\Enum\TypeTask">')]
        private readonly TypeTask $typeTask,
        #[Serializer\SerializedName(name: 'firstRequestDelay')]
        #[Serializer\Type('integer')]
        private readonly int $firstRequestDelay,
        #[Serializer\SerializedName(name: 'requestInterval')]
        #[Serializer\Type('integer')]
        private readonly int $requestInterval,
        #[Serializer\SerializedName(name: 'timeout')]
        #[Serializer\Type('integer')]
        private readonly int $timeout
    ) {
    }

    public function getTypeTask(): TypeTask
    {
        return $this->typeTask;
    }

    public function getFirstRequestDelay(): int
    {
        return $this->firstRequestDelay;
    }

    public function getRequestInterval(): int
    {
        return $this->requestInterval;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }
}