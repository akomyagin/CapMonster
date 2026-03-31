<?php

declare(strict_types=1);

namespace Tests\Unit;

use CapMonsterClient\CapMonsterClient;
use CapMonsterClient\CapMonsterConfiguration;
use CapMonsterClient\Dto\Solution\ReCaptchaSolution;
use CapMonsterClient\Dto\Task\NoCaptchaTask;
use CapMonsterClient\Enum\TypeTask;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Stream;
use PHPUnit\Framework\MockObject\Exception as UnitException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;

final class CapMonsterTaskTest extends AbstractTestCase
{
    private const G_RECAPTCHA = '3AHJ_VuvYIBNBW5yyv0zRYJ75VkOKvhKj9_xGBJKnQimF72rfoq3Iy-DyGHMwLAo6a3';

    private const TASK_ID = 7654321;

    private const TIMEOUT_FOR_TASK = [
        'taskType' => TypeTask::NO_CAPTCHA_TASK_PROXYLESS,
        'firstRequestDelay' => 1,
        'requestInterval' => 1,
        'timeout' => 10,
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
     * @throws ClientExceptionInterface
     */
    public function testTask(): void
    {
        $client = new CapMonsterClient(
            $this->createClientMock(),
            $this->configuration
        );
        /** @var ReCaptchaSolution $solution */
        $solution = $client->runTask(new NoCaptchaTask('localhost', 'webKey'));
        $this->assertSame(self::G_RECAPTCHA, $solution->getGRecaptchaResponse());
    }

    /**
     * @throws UnitException
     */
    private function createClientMock(): ClientInterface
    {
        $mock = $this->createMock(ClientInterface::class);
        $mock
            ->expects($this->exactly(3))
            ->method('sendRequest')
            ->willReturnOnConsecutiveCalls(
                $this->createResponse(1),
                $this->createResponse(2),
                $this->createResponse(3)
            );

        return $mock;
    }

    private function createResponse(int $count): Response
    {
        $json = match ($count) {
            1 => json_encode([
                'errorId' => 0,
                'taskId' => self::TASK_ID,
            ]),
            3 => json_encode([
                'errorId' => 0,
                'status' => 'ready',
                'solution' => [
                    'gRecaptchaResponse' => self::G_RECAPTCHA,
                ],
            ]),
            default => json_encode([
                'errorId' => 0,
                'status' => 'processing',
            ]),
        };
        $json = $json ?: '{}';
        $content = sprintf('data://text/plain,%s', $json);
        $stream = new Stream($content, 'r');

        return (new Response())->withBody($stream);
    }
}