<?php

namespace CapMonsterClient\Transformer;

interface TransformerInterface
{
    public function transform(string $responseContent): object;
}