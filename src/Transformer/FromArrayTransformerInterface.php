<?php

declare(strict_types=1);

namespace CapMonsterClient\Transformer;

interface FromArrayTransformerInterface
{
    public function transform(string $className, array $data): object;
}