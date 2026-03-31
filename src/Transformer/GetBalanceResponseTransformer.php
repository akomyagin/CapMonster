<?php

declare(strict_types=1);

namespace CapMonsterClient\Transformer;

use CapMonsterClient\External\Dto\Response\GetBalanceResponse;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;

final class GetBalanceResponseTransformer implements TransformerInterface
{
    private SerializerBuilder $serializerBuilder;

    public function __construct(SerializerBuilder $serializerBuilder)
    {
        $this->serializerBuilder = $serializerBuilder;
    }

    public function transform(string $responseContent): GetBalanceResponse
    {
        $serializer = $this->serializerBuilder->build();

        return
            $serializer->deserialize(
                $responseContent,
                GetBalanceResponse::class,
                'json'
            );
    }
}