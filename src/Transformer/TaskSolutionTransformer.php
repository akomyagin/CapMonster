<?php

declare(strict_types=1);

namespace CapMonsterClient\Transformer;

use CapMonsterClient\Dto\Solution\AbstractSolution;
use CapMonsterClient\Enum\TypeTask;
use CapMonsterClient\Resolver\TypeSolutionResolver;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;

final class TaskSolutionTransformer
{
    private SerializerBuilder $serializerBuilder;

    private TypeSolutionResolver $solutionResolver;

    public function __construct(
        SerializerBuilder $serializerBuilder = null,
        TypeSolutionResolver $solutionResolver = null
    ) {
        $this->serializerBuilder = $serializerBuilder ?? new SerializerBuilder();
        $this->solutionResolver = $solutionResolver ?? new TypeSolutionResolver();
    }

    /**
     * @throws \JsonException
     */
    public function transform(TypeTask $typeTask, string $responseContent): AbstractSolution
    {
        $classSolution = $this->solutionResolver->resolve($typeTask);

        $serializer = $this->serializerBuilder->build();
        $responseArray = json_decode($responseContent, true, 512, JSON_THROW_ON_ERROR);

        return $serializer->fromArray($responseArray['solution'], $classSolution);
    }
}