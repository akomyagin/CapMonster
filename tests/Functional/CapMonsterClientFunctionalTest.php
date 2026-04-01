<?php

declare(strict_types=1);

namespace Tests\Functional;

use CapMonsterClient\CapMonsterClient;
use CapMonsterClient\CapMonsterConfiguration;
use CapMonsterClient\Dto\Solution\ReCaptchaSolution;
use CapMonsterClient\Dto\Task\NoCaptchaTask;
use CapMonsterClient\Enum\TypeTask;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\FakeHttpClient\HttpClientHandlerFactory;
use Tests\Unit\AbstractTestCase;
use Webclient\Fake\FakeHttpClient;

#[CoversClass(CapMonsterClient::class)]
final class CapMonsterClientFunctionalTest extends AbstractTestCase
{
    private function proxylessRecaptchaTimeoutConfig(): CapMonsterConfiguration
    {
        return new CapMonsterConfiguration(self::SECRET_KEY, [
            'timeouts' => [
                [
                    'taskType' => TypeTask::NO_CAPTCHA_TASK_PROXYLESS,
                    'firstRequestDelay' => 0,
                    'requestInterval' => 0,
                    'timeout' => 120,
                ],
            ],
        ]);
    }

    public function testRunTaskPollsUntilReadyAccordingToApiFlow(): void
    {
        $configuration = $this->proxylessRecaptchaTimeoutConfig();
        $factory = new HttpClientHandlerFactory($configuration->getBaseUrl(), [
            'getTaskResultSequence' => [
                ['errorId' => 0, 'status' => 'processing'],
                [
                    'errorId' => 0,
                    'status' => 'ready',
                    'solution' => ['gRecaptchaResponse' => 'integration-token-from-api-shape'],
                ],
            ],
        ]);
        $http = new FakeHttpClient($factory->create());
        $client = new CapMonsterClient($http, $configuration);

        $task = new NoCaptchaTask('https://example.com/page', 'site-key-value');
        /** @var ReCaptchaSolution $solution */
        $solution = $client->runTask($task);

        $this->assertSame(7654321, $task->getTaskId());
        $this->assertSame('integration-token-from-api-shape', $solution->getGRecaptchaResponse());
    }

    public function testGetBalanceThroughPublicClientUsesGetBalanceEndpoint(): void
    {
        $client = new CapMonsterClient(
            $this->createHttpClient(['balance' => '100']),
            $this->configuration
        );

        $this->assertSame(100.0, $client->getBalance());
    }
}
