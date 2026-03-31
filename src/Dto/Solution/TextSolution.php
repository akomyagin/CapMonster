<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Solution;

use JMS\Serializer\Annotation as Serializer;

final class TextSolution extends AbstractSolution
{
    public function __construct(
        #[Serializer\SerializedName(name: 'text')]
        #[Serializer\Type('string')]
        private readonly string $text
    ) {
    }

    public function getText(): string
    {
        return $this->text;
    }
}