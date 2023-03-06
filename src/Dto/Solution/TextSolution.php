<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Solution;

final class TextSolution extends AbstractSolution
{
    public function __construct(
        private readonly string $text
    ) {
    }

    public function getText(): string
    {
        return $this->text;
    }
}