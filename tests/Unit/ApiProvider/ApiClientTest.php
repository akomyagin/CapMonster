<?php

declare(strict_types=1);

namespace Tests\Unit\ApiProvider;

use CapMonsterClient\ApiProvider\ApiClient;
use CapMonsterClient\ApiProvider\Dto\Response\GetTaskResultResponse;
use CapMonsterClient\ApiProvider\Transformer\FromJsonTransformer;
use CapMonsterClient\CapMonsterConfiguration;
use CapMonsterClient\Common\Exception\CapMonsterException;
use CapMonsterClient\Dto\Task\NoCaptchaTask;
use CapMonsterClient\Enum\ErrorType;
use CapMonsterClient\Enum\StatusTask;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;
use Exception;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class ApiClientTest extends TestCase
{
    private CapMonsterConfiguration $configuration;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configuration = new CapMonsterConfiguration('secret-key');
    }

    public function testGetBalanceMapsApiErrorToCapMonsterException(): void
    {
        $http = $this->createMock(ClientInterface::class);
        $http->expects($this->once())->method('sendRequest')->willReturnCallback(
            function (RequestInterface $request): ResponseInterface {
                $this->assertStringEndsWith('/getBalance', $request->getUri()->getPath());

                return $this->jsonResponse(
                    '{"errorId":1,"errorCode":"ERROR_KEY_DOES_NOT_EXIST","errorDescription":"bad key"}'
                );
            }
        );

        $this->expectException(CapMonsterException::class);
        $this->expectExceptionMessage(ErrorType::INVALID_KEY->description());

        (new ApiClient($http, $this->configuration))->getBalance();
    }

    public function testNonTwoHundredStatusThrowsResponseCodeError(): void
    {
        $http = $this->createMock(ClientInterface::class);
        $http->method('sendRequest')->willReturn(
            $this->jsonResponse('{}', 503)
        );

        $this->expectException(CapMonsterException::class);
        $this->expectExceptionMessage(ErrorType::RESPONSE_CODE_ERROR->description());

        (new ApiClient($http, $this->configuration))->getBalance();
    }

    public function testClientExceptionIsWrappedAsSendMessageError(): void
    {
        $http = $this->createMock(ClientInterface::class);
        $http->method('sendRequest')->willThrowException(
            new class ('network down') extends Exception implements ClientExceptionInterface {
            }
        );

        $this->expectException(CapMonsterException::class);
        $this->expectExceptionMessage(ErrorType::SEND_MESSAGE_ERROR->description());

        (new ApiClient($http, $this->configuration))->getBalance();
    }

    public function testMalformedJsonThrowsResponseError(): void
    {
        $http = $this->createMock(ClientInterface::class);
        $http->method('sendRequest')->willReturn(
            $this->rawResponse('{not-json', 200)
        );

        $this->expectException(CapMonsterException::class);
        $this->expectExceptionMessage(ErrorType::RESPONSE_ERROR->description());

        (new ApiClient($http, $this->configuration))->getBalance();
    }

    public function testCreateTaskReturnsDtoFromSuccessfulResponse(): void
    {
        $http = $this->createMock(ClientInterface::class);
        $http->expects($this->once())->method('sendRequest')->willReturn(
            $this->jsonResponse('{"errorId":0,"taskId":7654321}')
        );

        $dto = (new ApiClient($http, $this->configuration))->createTask(
            new NoCaptchaTask('https://example.com', 'site-key')
        );

        $this->assertSame(7654321, $dto->getTaskId());
        $this->assertSame(0, $dto->getErrorId());
    }

    public function testGetResultTaskDeserializesProcessingStatus(): void
    {
        $http = $this->createMock(ClientInterface::class);
        $http->expects($this->once())->method('sendRequest')->willReturn(
            $this->jsonResponse('{"errorId":0,"status":"processing"}')
        );

        $task = new NoCaptchaTask('https://example.com', 'site-key');
        $task->setTaskId(10);

        $response = (new ApiClient($http, $this->configuration))->getResultTask($task);

        $this->assertSame(StatusTask::PROCESSING, $response->getStatus());
    }

    public function testExtractTaskSolutionThrowsWhenSolutionMalformed(): void
    {
        $http = $this->createMock(ClientInterface::class);
        $api = new ApiClient($http, $this->configuration);

        $task = new NoCaptchaTask('https://example.com', 'site-key');
        $response = (new FromJsonTransformer(new SerializerBuilder(), GetTaskResultResponse::class))->transform(
            '{"errorId":0,"status":"ready","solution":{"gRecaptchaResponse":[]}}'
        );

        $this->assertInstanceOf(GetTaskResultResponse::class, $response);

        $this->expectException(CapMonsterException::class);
        $this->expectExceptionMessage(ErrorType::RESPONSE_ERROR->description());

        $api->extractTaskSolution($task->getType(), $response);
    }

    private function jsonResponse(string $json, int $status = 200): ResponseInterface
    {
        return $this->rawResponse($json, $status);
    }

    private function rawResponse(string $body, int $status): ResponseInterface
    {
        $handle = fopen('php://memory', 'wb+');
        fwrite($handle, $body);
        rewind($handle);

        return (new Response())
            ->withStatus($status)
            ->withBody(new Stream($handle));
    }
}
