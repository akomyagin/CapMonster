<?php

declare(strict_types=1);

namespace CapMonsterClient\Transformer;

use CapMonsterClient\External\Dto\Response\GetTaskResultResponse;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;

final class GetTaskResultResponseTransformer implements TransformerInterface
{
    private SerializerBuilder $serializerBuilder;

    public function __construct(SerializerBuilder $serializerBuilder)
    {
        $this->serializerBuilder = $serializerBuilder;
    }

    public function transform(string $responseContent): GetTaskResultResponse
    {
        $serializer = $this->serializerBuilder->build();

        return
            $serializer->deserialize(
                $responseContent,
                GetTaskResultResponse::class,
                'json'
            );
    }
}