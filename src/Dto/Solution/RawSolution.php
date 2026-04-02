<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Solution;

final class RawSolution extends AbstractSolution
{
    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        private readonly array $payload
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function getPayload(): array
    {
        return $this->payload;
    }
}
