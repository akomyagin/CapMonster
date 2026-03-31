<?php

declare(strict_types=1);

namespace Tests\Unit\Resolver;

use CapMonsterClient\Enum\ApiMethod;
use CapMonsterClient\Resolver\TransformResolver;
use CapMonsterClient\Transformer\CreateTaskResponseTransformer;
use CapMonsterClient\Transformer\GetBalanceResponseTransformer;
use CapMonsterClient\Transformer\GetTaskResultResponseTransformer;
use PHPUnit\Framework\TestCase;

final class TransformResolverTest extends TestCase
{
    public function testResolveCreateTaskTransformer(): void
    {
        $resolver = new TransformResolver();

        $transformer = $resolver->resolve(ApiMethod::CREATE_TASK);

        $this->assertInstanceOf(CreateTaskResponseTransformer::class, $transformer);
    }

    public function testResolveGetBalanceTransformer(): void
    {
        $resolver = new TransformResolver();

        $transformer = $resolver->resolve(ApiMethod::GET_BALANCE);

        $this->assertInstanceOf(GetBalanceResponseTransformer::class, $transformer);
    }

    public function testResolveGetTaskResultTransformer(): void
    {
        $resolver = new TransformResolver();

        $transformer = $resolver->resolve(ApiMethod::GET_TASK_RESULT);

        $this->assertInstanceOf(GetTaskResultResponseTransformer::class, $transformer);
    }
}
