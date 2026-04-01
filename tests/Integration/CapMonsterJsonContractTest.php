<?php

declare(strict_types=1);

namespace Tests\Integration;

use CapMonsterClient\ApiProvider\ApiClient;
use CapMonsterClient\CapMonsterConfiguration;
use CapMonsterClient\Dto\Task\NoCaptchaTask;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Фиксирует фактический JSON тел запросов к API (создание задачи, результат).
 * Имена полей задачи сериализуются в snake_case (website_url, website_key), плюс clientKey и taskId
 * как в документации {@link https://docs.capmonster.cloud/ru/docs/api/methods/create-task/}.
 */
final class CapMonsterJsonContractTest extends TestCase
{
    public function testCreateTaskRequestBodyMatchesDocumentation(): void
    {
        $configuration = new CapMonsterConfiguration('API_KEY_fixture');

        $http = $this->createMock(ClientInterface::class);
        $http->expects($this->once())->method('sendRequest')->willReturnCallback(
            function (RequestInterface $request) use ($configuration): ResponseInterface {
                $this->assertSame('POST', $request->getMethod());
                $this->assertStringEndsWith('/createTask', $request->getUri()->getPath());

                $payload = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
                $this->assertSame('API_KEY_fixture', $payload['clientKey']);
                $this->assertArrayHasKey('task', $payload);
                $this->assertSame('RecaptchaV2TaskProxyless', $payload['task']['type']);
                $this->assertSame(
                    'https://lessons.zennolab.com/captchas/recaptcha/v2_simple.php',
                    $payload['task']['website_url']
                );
                $this->assertSame(
                    '6Lcg7CMUAAAAANphynKgn9YAgA4tQ2KI_iqRyTwd',
                    $payload['task']['website_key']
                );

                return $this->jsonResponse('{"errorId":0,"taskId":1}');
            }
        );

        (new ApiClient($http, $configuration))->createTask(
            new NoCaptchaTask(
                'https://lessons.zennolab.com/captchas/recaptcha/v2_simple.php',
                '6Lcg7CMUAAAAANphynKgn9YAgA4tQ2KI_iqRyTwd'
            )
        );
    }

    public function testGetTaskResultRequestBodyMatchesDocumentation(): void
    {
        $configuration = new CapMonsterConfiguration('API_KEY_fixture');
        $http = $this->createMock(ClientInterface::class);
        $http->expects($this->once())->method('sendRequest')->willReturnCallback(
            function (RequestInterface $request): ResponseInterface {
                $this->assertStringEndsWith('/getTaskResult', $request->getUri()->getPath());
                $payload = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
                $this->assertSame('API_KEY_fixture', $payload['clientKey']);
                $this->assertSame(7654321, $payload['taskId']);

                return $this->jsonResponse(
                    '{"errorId":0,"status":"processing"}'
                );
            }
        );

        $task = new NoCaptchaTask('https://example.com', 'k');
        $task->setTaskId(7654321);

        (new ApiClient($http, $configuration))->getResultTask($task);
    }

    private function jsonResponse(string $json, int $status = 200): ResponseInterface
    {
        $handle = fopen('php://memory', 'wb+');
        fwrite($handle, $json);
        rewind($handle);

        return (new Response())
            ->withStatus($status)
            ->withBody(new Stream($handle));
    }
}
