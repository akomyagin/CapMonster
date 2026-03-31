<?php

declare(strict_types=1);

namespace CapMonsterClient\Transformer;

use CapMonsterClient\External\Dto\Response\AbstractResponse;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;
use JMS\Serializer\DeserializationContext;

final class JsonToResponseTransformer implements TransformerInterface
{
    public function __construct(
        private readonly SerializerBuilder $serializerBuilder,
        private readonly string $className,
        private readonly ?DeserializationContext $context = null
    ) {
    }

    public function transform(string $responseContent): AbstractResponse
    {
        return
            $this->serializerBuilder->build()->deserialize(
                $responseContent,
                type: $this->className,
                format: 'json',
                context: $this->context
            );
    }
}