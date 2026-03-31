<?php

declare(strict_types=1);

namespace CapMonsterClient\Common\Dto\Request;

use CapMonsterClient\Enum\ApiMethod;

abstract class AbstractRequest
{
    public function __construct(
        private readonly string $clientKey
    ) {
    }

    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    abstract public function getMethod(): ApiMethod;
}