<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Solution;

final class TokenSolution extends AbstractSolution
{
    public function __construct(
        private readonly string $token
    ) {
    }

    public function getToken(): string
    {
        return $this->token;
    }
}