<?php

declare(strict_types=1);

namespace Tests\Unit;

use CapMonsterClient\CapMonsterClient;
use CapMonsterClient\CapMonsterConfiguration;
use CapMonsterClient\Common\Exception\CapMonsterException;
use CapMonsterClient\Dto\Task\NoCaptchaTask;
use CapMonsterClient\Enum\TypeTask;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Stream;
use PHPUnit\Framework\MockObject\Exception as UnitException;
use Psr\Http\Client\ClientInterface;

final class CapMonsterTaskLimitTest extends AbstractTestCase
{
    private const TASK_ID = 7654321;

    private const TIMEOUT_FOR_TASK = [
        'taskType' => TypeTask::NO_CAPTCHA_TASK_PROXYLESS,
        'firstRequestDelay' => 0,
        'requestInterval' => 0,
        'timeout' => 1000,
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration = new CapMonsterConfiguration(
            self::SECRET_KEY,
            ['timeouts' => [self::TIMEOUT_FOR_TASK]]
        );
    }

    /**
     * @throws UnitException
     */
    public function testRunTaskStopsOnPollingLimit(): void
    {
        $client = new CapMonsterClient(
            $this->createClientMock(),
            $this->configuration
        );

        $this->expectException(CapMonsterException::class);
        $client->runTask(new NoCaptchaTask('localhost', 'webKey'));
    }

    /**
     * @throws UnitException
     */
    private function createClientMock(): ClientInterface
    {
        $mock = $this->createMock(ClientInterface::class);
        $call = 0;
        $mock
            ->method('sendRequest')
            ->willReturnCallback(function () use (&$call): Response {
                ++$call;
                if ($call === 1) {
                    return $this->createResponseCreateTask();
                }

                return $this->createResponseProcessing();
            });

        return $mock;
    }

    private function createResponseCreateTask(): Response
    {
        $json = json_encode([
            'errorId' => 0,
            'taskId' => self::TASK_ID,
        ]) ?: '{}';
        $stream = new Stream(sprintf('data://text/plain,%s', $json), 'r');

        return (new Response())->withBody($stream);
    }

    private function createResponseProcessing(): Response
    {
        $json = json_encode([
            'errorId' => 0,
            'status' => 'processing',
        ]) ?: '{}';
        $stream = new Stream(sprintf('data://text/plain,%s', $json), 'r');

        return (new Response())->withBody($stream);
    }
}
