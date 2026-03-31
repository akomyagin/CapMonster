<?php

declare(strict_types=1);

namespace CapMonsterClient\External\Transformer;

interface FromDtoTransformerInterface
{
    public function transform(object $object): string|array;
}