<?php

declare(strict_types=1);

namespace CapMonsterClient\External\Transformer;

interface ToDtoTransformerInterface
{
    public function transform(string $data): object;
}