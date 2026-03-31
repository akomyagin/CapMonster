<?php

declare(strict_types=1);

namespace CapMonsterClient\Transformer;

use CapMonsterClient\Serializer\Builder\SerializerBuilder;
use JMS\Serializer\DeserializationContext;

final class FromArrayTransformer implements FromArrayTransformerInterface
{
    private readonly ?SerializerBuilder $serializerBuilder;

    public function __construct(
        ?SerializerBuilder $serializerBuilder = null,
        private readonly ?DeserializationContext $context = null
    ) {
        $this->serializerBuilder = $serializerBuilder ?? new SerializerBuilder();
    }

    public function transform(string $className, array $data): object
    {
        return
            $this->serializerBuilder->build()->fromArray(
                $data,
                $className,
                $this->context
            );
    }
}