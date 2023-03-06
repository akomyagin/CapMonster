<?php

declare(strict_types=1);

namespace CapMonsterClient;

final class CapMonsterConfiguration
{
    public function __construct(
        private string $clientKey
    ) {
    }

    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    public function setClientKey(string $clientKey): void
    {
        $this->clientKey = $clientKey;
    }
}