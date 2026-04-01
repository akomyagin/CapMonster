<?php

declare(strict_types=1);

namespace Tests\Integration;

use CapMonsterClient\ApiProvider\ApiClient;
use CapMonsterClient\Dto\Solution\ReCaptchaSolution;
use CapMonsterClient\Dto\Task\NoCaptchaTask;
use CapMonsterClient\Enum\StatusTask;
use CapMonsterClient\Enum\TypeTask;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Unit\AbstractTestCase;

#[CoversClass(ApiClient::class)]
final class ApiClientIntegrationTest extends AbstractTestCase
{
    public function testGetBalanceAgainstFakeCapMonsterHttpStack(): void
    {
        $http = $this->createHttpClient(['balance' => '77.25']);
        $api = new ApiClient($http, $this->configuration);

        $this->assertSame(77.25, $api->getBalance());
    }

    public function testCreateTaskReturnsTaskIdFromJsonResponse(): void
    {
        $http = $this->createHttpClient();
        $api = new ApiClient($http, $this->configuration);

        $response = $api->createTask(new NoCaptchaTask('https://lessons.example.com/form', '6LcSiteKey'));

        $this->assertSame(7654321, $response->getTaskId());
        $this->assertFalse($response->isError());
    }

    public function testGetResultTaskAndExtractSolutionRoundTrip(): void
    {
        $http = $this->createHttpClient();
        $api = new ApiClient($http, $this->configuration);

        $task = new NoCaptchaTask('https://example.com', 'key');
        $task->setTaskId(7654321);

        $result = $api->getResultTask($task);

        $this->assertSame(StatusTask::READY, $result->getStatus());
        $solution = $api->extractTaskSolution(TypeTask::NO_CAPTCHA_TASK_PROXYLESS, $result);

        $this->assertInstanceOf(ReCaptchaSolution::class, $solution);
        $this->assertSame('fake-response-token', $solution->getGRecaptchaResponse());
    }
}
