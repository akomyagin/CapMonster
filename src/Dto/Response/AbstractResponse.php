<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Response;

abstract class AbstractResponse
{
    public function __construct(
        private readonly int $errorId,
        private readonly ?string $errorCode = null
    ) {
    }

    public function getErrorId(): int
    {
        return $this->errorId;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }
}