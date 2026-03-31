<?php

declare(strict_types=1);

namespace CapMonsterClient\Transformer;

use CapMonsterClient\External\Dto\Response\CreateTaskResponse;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;

final class CreateTaskResponseTransformer implements TransformerInterface
{
    private SerializerBuilder $serializerBuilder;

    public function __construct(SerializerBuilder $serializerBuilder)
    {
        $this->serializerBuilder = $serializerBuilder;
    }

    public function transform(string $responseContent): CreateTaskResponse
    {
        $serializer = $this->serializerBuilder->build();

        return
            $serializer->deserialize(
                $responseContent,
                CreateTaskResponse::class,
                'json'
            );
    }
}