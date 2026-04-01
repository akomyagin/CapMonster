<?php

declare(strict_types=1);

namespace CapMonsterClient\ApiProvider\Transformer;

interface ToDtoTransformerInterface
{
    public function transform(string $data): object;
}
