<?php

declare(strict_types=1);

namespace Tests\Functional;

use CapMonsterClient\CapMonsterClient;
use CapMonsterClient\CapMonsterConfiguration;
use CapMonsterClient\Dto\Solution\TextSolution;
use CapMonsterClient\Dto\Solution\TokenSolution;
use CapMonsterClient\Dto\Task\ImageToTextTask;
use CapMonsterClient\Dto\Task\TurnstileTask;
use CapMonsterClient\Enum\TypeTask;
use PHPUnit\Framework\TestCase;
use Tests\Support\RecordingHandler;
use Webclient\Fake\FakeHttpClient;

/**
 * Full runTask() poll loop through the public facade with instant (0s) poll timings.
 */
final class CapMonsterClientRunTaskTest extends TestCase
{
    private RecordingHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new RecordingHandler();
    }

    /**
     * @param array<string, mixed> $config
     */
    private function client(array $config = []): CapMonsterClient
    {
        $timeouts = [];
        foreach (TypeTask::cases() as $typeTask) {
            $timeouts[] = [
                'taskType' => $typeTask,
                'firstRequestDelay' => 0,
                'requestInterval' => 0,
                'timeout' => 60,
            ];
        }

        return new CapMonsterClient(
            new FakeHttpClient($this->handler),
            new CapMonsterConfiguration('KEY', $config + ['timeouts' => $timeouts])
        );
    }

    public function testRunTaskPollsUntilReadyAndReturnsTokenSolution(): void
    {
        $this->handler
            ->pushJson(['errorId' => 0, 'taskId' => 777])
            ->pushJson(['errorId' => 0, 'status' => 'processing'])
            ->pushJson(['errorId' => 0, 'status' => 'processing'])
            ->pushJson([
                'errorId' => 0,
                'status' => 'ready',
                'solution' => ['token' => 'TKN', 'userAgent' => 'UA'],
            ]);

        $task = new TurnstileTask('https://x.test/', 'sitekey');
        $solution = $this->client()->runTask($task);

        self::assertInstanceOf(TokenSolution::class, $solution);
        self::assertSame('TKN', $solution->getToken());
        self::assertSame('UA', $solution->getUserAgent());

        // task id from createTask is propagated back onto the task object
        self::assertSame(777, $task->getTaskId());

        // request sequence: 1x createTask, then 3x getTaskResult with the returned id
        self::assertSame(4, $this->handler->requestCount());
        self::assertSame('https://api.capmonster.cloud/createTask', $this->handler->request(0)['uri']);
        foreach ([1, 2, 3] as $index) {
            self::assertSame('https://api.capmonster.cloud/getTaskResult', $this->handler->request($index)['uri']);
            self::assertSame('{"clientKey":"KEY","taskId":777}', $this->handler->request($index)['body']);
        }
    }

    public function testRunTaskImmediatelyReadyReturnsTextSolution(): void
    {
        $this->handler
            ->pushJson(['errorId' => 0, 'taskId' => 5])
            ->pushJson(['errorId' => 0, 'status' => 'ready', 'solution' => ['text' => 'w0rd']]);

        $solution = $this->client()->runTask(new ImageToTextTask('B64'));

        self::assertInstanceOf(TextSolution::class, $solution);
        self::assertSame('w0rd', $solution->getText());
        self::assertSame(2, $this->handler->requestCount());
    }
}
