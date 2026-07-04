<?php

declare(strict_types=1);

namespace Tests\Unit\ApiProvider\Dto\Response;

use CapMonsterClient\ApiProvider\Dto\Response\CreateTaskResponse;
use CapMonsterClient\ApiProvider\Dto\Response\GetBalanceResponse;
use CapMonsterClient\ApiProvider\Dto\Response\GetTaskResultResponse;
use CapMonsterClient\ApiProvider\Transformer\FromJsonTransformer;
use CapMonsterClient\Enum\StatusTask;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;
use PHPUnit\Framework\TestCase;

final class ResponseDeserializationTest extends TestCase
{
    /**
     * @template T of object
     *
     * @param class-string<T> $className
     *
     * @return T
     */
    private function fromJson(string $json, string $className): object
    {
        $object = (new FromJsonTransformer(new SerializerBuilder(), $className))->transform($json);
        assert($object instanceof $className);

        return $object;
    }

    public function testCreateTaskResponseSuccess(): void
    {
        $response = $this->fromJson('{"errorId":0,"taskId":123}', CreateTaskResponse::class);

        self::assertFalse($response->isError());
        self::assertSame(123, $response->getTaskId());
        self::assertSame(0, $response->getErrorId());
        self::assertSame('', $response->getErrorCode());
    }

    public function testCreateTaskResponseErrorWithoutTaskId(): void
    {
        $response = $this->fromJson('{"errorId":1,"errorCode":"ERROR_ZERO_BALANCE"}', CreateTaskResponse::class);

        self::assertTrue($response->isError());
        self::assertSame(1, $response->getErrorId());
        self::assertSame('ERROR_ZERO_BALANCE', $response->getErrorCode());
        self::assertSame(0, $response->getTaskId());
    }

    public function testGetBalanceResponseSuccess(): void
    {
        $response = $this->fromJson('{"errorId":0,"balance":345.67}', GetBalanceResponse::class);

        self::assertFalse($response->isError());
        self::assertSame(345.67, $response->getBalance());
    }

    public function testGetTaskResultResponseProcessing(): void
    {
        $response = $this->fromJson('{"errorId":0,"status":"processing"}', GetTaskResultResponse::class);

        self::assertFalse($response->isError());
        self::assertSame(StatusTask::PROCESSING, $response->getStatus());
        self::assertSame([], $response->getSolution());
    }

    public function testGetTaskResultResponseReadyWithSolution(): void
    {
        $response = $this->fromJson(
            '{"errorId":0,"status":"ready","solution":{"token":"T","userAgent":"UA"}}',
            GetTaskResultResponse::class
        );

        self::assertSame(StatusTask::READY, $response->getStatus());
        self::assertSame(['token' => 'T', 'userAgent' => 'UA'], $response->getSolution());
    }

    public function testResponsesTolerateMissingErrorId(): void
    {
        // Defensive: when the API omits "errorId"/"errorCode" the response behaves as error-free
        // instead of crashing on an uninitialized readonly property.
        $response = $this->fromJson('{"balance":10.5}', GetBalanceResponse::class);

        self::assertFalse($response->isError());
        self::assertSame(0, $response->getErrorId());
        self::assertSame('', $response->getErrorCode());
        self::assertSame(10.5, $response->getBalance());
    }
}
