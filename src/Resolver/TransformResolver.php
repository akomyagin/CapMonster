<?php

declare(strict_types=1);

namespace CapMonsterClient\Resolver;

use CapMonsterClient\Common\Exception\CapMonsterException;
use CapMonsterClient\Enum\ApiMethod;
use CapMonsterClient\Enum\ErrorType;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;
use CapMonsterClient\Transformer\CreateTaskResponseTransformer;
use CapMonsterClient\Transformer\GetBalanceResponseTransformer;
use CapMonsterClient\Transformer\GetTaskResultResponseTransformer;
use CapMonsterClient\Transformer\TransformerInterface;

final class TransformResolver
{
    private SerializerBuilder $serializerBuilder;

    public function __construct(?SerializerBuilder $serializerBuilder = null)
    {
        $this->serializerBuilder = $serializerBuilder ?? new SerializerBuilder();
    }

    /**
     * @throws CapMonsterException
     */
    public function resolve(ApiMethod $apiMethod): TransformerInterface
    {
        switch ($apiMethod) {
            case ApiMethod::CREATE_TASK:
                return new CreateTaskResponseTransformer($this->serializerBuilder);
            case ApiMethod::GET_TASK_RESULT:
                return new GetTaskResultResponseTransformer($this->serializerBuilder);
            case ApiMethod::GET_BALANCE:
                return new GetBalanceResponseTransformer($this->serializerBuilder);
        }

        throw new CapMonsterException(ErrorType::INVALID_ARGUMENT_EXCEPTION);
    }
}