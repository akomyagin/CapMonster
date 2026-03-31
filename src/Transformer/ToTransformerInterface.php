<?php

declare(strict_types=1);

namespace CapMonsterClient\Transformer;

interface ToTransformerInterface
{
    public function transform(object $data): string|array;
}