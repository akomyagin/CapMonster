<?php

declare(strict_types=1);

namespace CapMonsterClient\Transformer;

interface ToTransformerInterface
{
    /**
     * @return string|array<string, mixed>
     */
    public function transform(object $data): string|array;
}