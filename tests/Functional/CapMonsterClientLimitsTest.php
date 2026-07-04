<?php

declare(strict_types=1);

namespace Tests\Functional;

use CapMonsterClient\CapMonsterClient;
use CapMonsterClient\CapMonsterConfiguration;
use CapMonsterClient\Common\Exception\CapMonsterException;
use CapMonsterClient\Dto\Task\TurnstileTask;
use CapMonsterClient\Enum\ErrorType;
use CapMonsterClient\Enum\TypeTask;
use PHPUnit\Framework\TestCase;
use Tests\Support\RecordingHandler;
use Webclient\Fake\FakeHttpClient;

/**
 * Failure modes of the runTask() poll loop, driven with near-zero timings.
 */
final class CapMonsterClientLimitsTest extends TestCase
{
    private RecordingHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new RecordingHandler();
    }

    /**
     * @param array<string, mixed> $config
     */
    private function client(array $timeoutRow, array $config = []): CapMonsterClient
    {
        $timeouts = [];
        foreach (TypeTask::cases() as $typeTask) {
            $timeouts[] = ['taskType' => $typeTask] + $timeoutRow;
        }

        return new CapMonsterClient(
            new FakeHttpClient($this->handler),
            new CapMonsterConfiguration('KEY', $config + ['timeouts' => $timeouts])
        );
    }

    public function testRequestLimitExceededWhenMaxAttemptsIsReached(): void
    {
        $this->handler->pushJson(['errorId' => 0, 'taskId' => 1]);
        // two allowed polling attempts, both still processing
        $this->handler->pushJson(['errorId' => 0, 'status' => 'processing']);
        $this->handler->pushJson(['errorId' => 0, 'status' => 'processing']);

        $client = $this->client(
            ['firstRequestDelay' => 0, 'requestInterval' => 0, 'timeout' => 60],
            ['maxGetTaskResultAttempts' => 2]
        );

        try {
            $client->runTask(new TurnstileTask('https://x.test/', 'k'));
            self::fail('Expected CapMonsterException was not thrown');
        } catch (CapMonsterException $exception) {
            self::assertSame(ErrorType::REQUEST_LIMIT_EXCEEDED, $exception->getType());
        }

        // 1 createTask + exactly maxGetTaskResultAttempts polls
        self::assertSame(3, $this->handler->requestCount());
    }

    public function testTimeoutExpiredWhenPollBudgetIsExhausted(): void
    {
        $this->handler->pushJson(['errorId' => 0, 'taskId' => 1]);
        $this->handler->pushJson(['errorId' => 0, 'status' => 'processing']);

        // timeout=0 with requestInterval=1: the first re-poll step exceeds the budget
        // and throws before sleeping, so the test stays fast.
        $client = $this->client(['firstRequestDelay' => 0, 'requestInterval' => 1, 'timeout' => 0]);

        try {
            $client->runTask(new TurnstileTask('https://x.test/', 'k'));
            self::fail('Expected CapMonsterException was not thrown');
        } catch (CapMonsterException $exception) {
            self::assertSame(ErrorType::TIMEOUT_EXPIRED, $exception->getType());
        }

        self::assertSame(2, $this->handler->requestCount());
    }

    public function testCreateTaskErrorAbortsBeforeAnyPolling(): void
    {
        $this->handler->pushJson(['errorId' => 1, 'errorCode' => 'ERROR_ZERO_BALANCE']);

        $client = $this->client(['firstRequestDelay' => 0, 'requestInterval' => 0, 'timeout' => 60]);

        try {
            $client->runTask(new TurnstileTask('https://x.test/', 'k'));
            self::fail('Expected CapMonsterException was not thrown');
        } catch (CapMonsterException $exception) {
            self::assertSame(ErrorType::NO_FUNDS, $exception->getType());
        }

        self::assertSame(1, $this->handler->requestCount());
    }

    public function testPollErrorCodePropagates(): void
    {
        $this->handler->pushJson(['errorId' => 0, 'taskId' => 9]);
        $this->handler->pushJson(['errorId' => 1, 'errorCode' => 'ERROR_NO_SUCH_CAPCHA_ID']);

        $client = $this->client(['firstRequestDelay' => 0, 'requestInterval' => 0, 'timeout' => 60]);

        try {
            $client->runTask(new TurnstileTask('https://x.test/', 'k'));
            self::fail('Expected CapMonsterException was not thrown');
        } catch (CapMonsterException $exception) {
            self::assertSame(ErrorType::CAPTCHA_ID_IS_NOT_FOUND, $exception->getType());
        }
    }
}
