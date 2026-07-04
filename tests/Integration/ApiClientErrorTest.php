<?php

declare(strict_types=1);

namespace Tests\Integration;

use CapMonsterClient\ApiProvider\ApiClient;
use CapMonsterClient\CapMonsterConfiguration;
use CapMonsterClient\Common\Exception\CapMonsterException;
use CapMonsterClient\Dto\Task\ImageToTextTask;
use CapMonsterClient\Enum\ErrorType;
use PHPUnit\Framework\TestCase;
use Tests\Support\RecordingHandler;
use Tests\Support\ThrowingHandler;
use Webclient\Fake\FakeHttpClient;

final class ApiClientErrorTest extends TestCase
{
    private RecordingHandler $handler;

    private ApiClient $apiClient;

    protected function setUp(): void
    {
        $this->handler = new RecordingHandler();
        $this->apiClient = new ApiClient(
            new FakeHttpClient($this->handler),
            new CapMonsterConfiguration('KEY')
        );
    }

    private function assertThrowsErrorType(ErrorType $expected, callable $call): void
    {
        try {
            $call();
            self::fail('Expected CapMonsterException was not thrown');
        } catch (CapMonsterException $exception) {
            self::assertSame($expected, $exception->getType());
            self::assertSame($expected->description(), $exception->getMessage());
        }
    }

    public function testKnownApiErrorCodeIsMappedToErrorType(): void
    {
        $this->handler->pushJson(['errorId' => 1, 'errorCode' => 'ERROR_KEY_DOES_NOT_EXIST']);

        $this->assertThrowsErrorType(ErrorType::INVALID_KEY, fn () => $this->apiClient->getBalance());
    }

    public function testZeroBalanceErrorOnCreateTask(): void
    {
        $this->handler->pushJson(['errorId' => 1, 'errorCode' => 'ERROR_ZERO_BALANCE']);

        $this->assertThrowsErrorType(
            ErrorType::NO_FUNDS,
            fn () => $this->apiClient->createTask(new ImageToTextTask('B64'))
        );
    }

    public function testCaptchaUnsolvableErrorOnGetResult(): void
    {
        $this->handler->pushJson(['errorId' => 1, 'errorCode' => 'ERROR_CAPTCHA_UNSOLVABLE']);
        $task = new ImageToTextTask('B64');
        $task->setTaskId(1);

        $this->assertThrowsErrorType(
            ErrorType::CAPTCHA_UNSOLVABLE,
            fn () => $this->apiClient->getResultTask($task)
        );
    }

    public function testUnknownErrorCodeFallsBackToUnknownError(): void
    {
        $this->handler->pushJson(['errorId' => 1, 'errorCode' => 'ERROR_NOT_IN_THE_ENUM']);

        $this->assertThrowsErrorType(ErrorType::UNKNOWN_ERROR, fn () => $this->apiClient->getBalance());
    }

    public function testNon2xxStatusCodeMapsToResponseCodeError(): void
    {
        $this->handler->pushJson(['message' => 'gateway timeout'], 504);

        $this->assertThrowsErrorType(ErrorType::RESPONSE_CODE_ERROR, fn () => $this->apiClient->getBalance());
    }

    public function testNon2xxCarriesHttpStatusAsExceptionCode(): void
    {
        $this->handler->pushJson('{"oops":true}', 500);

        try {
            $this->apiClient->getBalance();
            self::fail('Expected CapMonsterException was not thrown');
        } catch (CapMonsterException $exception) {
            self::assertSame(ErrorType::RESPONSE_CODE_ERROR, $exception->getType());
            self::assertSame(500, $exception->getCode());
            self::assertNotNull($exception->getPrevious());
            self::assertSame('{"oops":true}', $exception->getPrevious()->getMessage());
        }
    }

    public function testMalformedJsonBodyMapsToResponseError(): void
    {
        $this->handler->pushText('this is not json', 200);

        $this->assertThrowsErrorType(ErrorType::RESPONSE_ERROR, fn () => $this->apiClient->getBalance());
    }

    public function testTransportFailureMapsToSendMessageError(): void
    {
        $apiClient = new ApiClient(
            new FakeHttpClient(new ThrowingHandler()),
            new CapMonsterConfiguration('KEY')
        );

        $this->assertThrowsErrorType(ErrorType::SEND_MESSAGE_ERROR, fn () => $apiClient->getBalance());
    }
}
