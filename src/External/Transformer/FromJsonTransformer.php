<?php

declare(strict_types=1);

namespace CapMonsterClient\External\Transformer;

use CapMonsterClient\Serializer\Builder\SerializerBuilder;
use JMS\Serializer\DeserializationContext;

final class FromJsonTransformer implements ToDtoTransformerInterface
{
    public function __construct(
        private readonly SerializerBuilder $serializerBuilder,
        private readonly string $className,
        private readonly ?DeserializationContext $context = null
    ) {
    }

    public function transform(string $data): object
    {
        return
            $this->serializerBuilder->build()->deserialize(
                $data,
                type: $this->className,
                format: 'json',
                context: $this->context
            );
    }
}