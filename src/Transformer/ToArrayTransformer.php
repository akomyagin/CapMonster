<?php

namespace CapMonsterClient\Transformer;

use CapMonsterClient\Serializer\Builder\SerializerBuilder;
use JMS\Serializer\DeserializationContext;

class ToArrayTransformer implements ToTransformerInterface
{
    public function __construct(
        private readonly SerializerBuilder $serializerBuilder,
        private readonly string $className,
        private readonly ?DeserializationContext $context = null
    ) {
    }

    public function transform(object $data): array
    {
        return
        $this->serializerBuilder->build()->toArray($data, $this->context);
    }
}