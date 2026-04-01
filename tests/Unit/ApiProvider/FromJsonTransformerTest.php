<?php

declare(strict_types=1);

namespace Tests\Unit\ApiProvider;

use CapMonsterClient\ApiProvider\Dto\Response\CreateTaskResponse;
use CapMonsterClient\ApiProvider\Dto\Response\GetBalanceResponse;
use CapMonsterClient\ApiProvider\Dto\Response\GetTaskResultResponse;
use CapMonsterClient\ApiProvider\Transformer\FromJsonTransformer;
use CapMonsterClient\Enum\StatusTask;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;
use PHPUnit\Framework\TestCase;

final class FromJsonTransformerTest extends TestCase
{
    public function testDeserializesBalanceResponse(): void
    {
        $transformer = new FromJsonTransformer(new SerializerBuilder(), GetBalanceResponse::class);
        $json = '{"errorId":0,"balance":12.5}';

        $dto = $transformer->transform($json);

        $this->assertInstanceOf(GetBalanceResponse::class, $dto);
        $this->assertSame(12.5, $dto->getBalance());
    }

    public function testDeserializesCreateTaskResponsePerApiDocumentation(): void
    {
        $transformer = new FromJsonTransformer(new SerializerBuilder(), CreateTaskResponse::class);
        $json = '{"errorId":0,"taskId":7654321}';

        $dto = $transformer->transform($json);

        $this->assertInstanceOf(CreateTaskResponse::class, $dto);
        $this->assertSame(7654321, $dto->getTaskId());
        $this->assertSame(0, $dto->getErrorId());
    }

    public function testDeserializesGetTaskResultProcessingShape(): void
    {
        $transformer = new FromJsonTransformer(new SerializerBuilder(), GetTaskResultResponse::class);
        $json = '{"errorId":0,"status":"processing"}';

        $dto = $transformer->transform($json);

        $this->assertInstanceOf(GetTaskResultResponse::class, $dto);
        $this->assertSame(StatusTask::PROCESSING, $dto->getStatus());
        $this->assertSame([], $dto->getSolution());
    }

    public function testDeserializesGetTaskResultReadyWithSolution(): void
    {
        $transformer = new FromJsonTransformer(new SerializerBuilder(), GetTaskResultResponse::class);
        $json = '{"errorId":0,"status":"ready","solution":{"text":"answer"}}';

        $dto = $transformer->transform($json);

        $this->assertSame(StatusTask::READY, $dto->getStatus());
        $this->assertSame(['text' => 'answer'], $dto->getSolution());
    }
}
