<?php

declare(strict_types=1);

namespace CapMonsterClient\Transformer;

interface FromArrayTransformerInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function transform(string $className, array $data): object;
}