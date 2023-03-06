<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Request;

abstract class AbstractRequest
{
    public function __construct(private readonly string $clientKey)
    {
    }

    public function getClientKey(): string
    {
        return $this->clientKey;
    }
}