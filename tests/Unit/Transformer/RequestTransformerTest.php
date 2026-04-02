<?php

declare(strict_types=1);

namespace Tests\Unit\Transformer;

use CapMonsterClient\ApiProvider\Request\Dto\CreateTaskRequest;
use CapMonsterClient\ApiProvider\Request\Dto\GetBalanceRequest;
use CapMonsterClient\ApiProvider\Request\Dto\GetTaskResultRequest;
use CapMonsterClient\Common\Dto\Request\AbstractRequest;
use CapMonsterClient\Dto\Task\ComplexImageTask;
use CapMonsterClient\Dto\Task\DataDomeTask;
use CapMonsterClient\Dto\Task\NoCaptchaTask;
use CapMonsterClient\Dto\Task\RecaptchaV3EnterpriseTask;
use CapMonsterClient\Dto\Task\TurnstileChallengeTask;
use CapMonsterClient\Dto\Task\TurnstileWaitingRoomTask;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;
use CapMonsterClient\Transformer\RequestTransformer;
use PHPUnit\Framework\TestCase;

final class RequestTransformerTest extends TestCase
{
    public function testCreateTaskUsesRecaptchaV2Alias(): void
    {
        $task = new NoCaptchaTask('https://example.com', 'site-key');
        $request = new CreateTaskRequest('secret', $task);

        $json = (new RequestTransformer(new SerializerBuilder()))->transform($request);
        $payload = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('RecaptchaV2TaskProxyless', $payload['task']['type']);
    }

    public function testGetBalancePayloadContainsOnlyClientKey(): void
    {
        $json = (new RequestTransformer(new SerializerBuilder()))->transform(
            new GetBalanceRequest('API_KEY')
        );
        $payload = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame(['clientKey' => 'API_KEY'], $payload);
    }

    public function testGetTaskResultPayloadMatchesApiDocumentation(): void
    {
        $task = new NoCaptchaTask('https://example.com', 'site-key');
        $task->setTaskId(7654321);

        $json = (new RequestTransformer(new SerializerBuilder()))->transform(
            new GetTaskResultRequest('API_KEY', $task)
        );
        $payload = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('API_KEY', $payload['clientKey']);
        $this->assertSame(7654321, $payload['taskId']);
    }

    public function testCreateTaskIncludesCallbackUrlWhenProvided(): void
    {
        $task = new NoCaptchaTask('https://example.com', 'site-key');
        $request = new CreateTaskRequest(
            'API_KEY',
            $task,
            'https://yourwebsite.com/callback'
        );

        $json = (new RequestTransformer(new SerializerBuilder()))->transform($request);
        $payload = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('https://yourwebsite.com/callback', $payload['callbackUrl']);
    }

    public function testCreateTaskPayloadForRecaptchaV3EnterpriseTask(): void
    {
        $task = new RecaptchaV3EnterpriseTask(
            'https://example.com',
            'site-key',
            0.7,
            'submit'
        );
        $payload = $this->toPayload(new CreateTaskRequest('API_KEY', $task));

        $this->assertSame('RecaptchaV3EnterpriseTaskProxyless', $payload['task']['type']);
        $this->assertSame(0.7, $payload['task']['min_score']);
        $this->assertSame('submit', $payload['task']['page_action']);
    }

    public function testCreateTaskPayloadForCloudflareTasks(): void
    {
        $challengePayload = $this->toPayload(new CreateTaskRequest(
            'API_KEY',
            new TurnstileChallengeTask('https://example.com', 'key')
        ));
        $waitingRoomPayload = $this->toPayload(new CreateTaskRequest(
            'API_KEY',
            new TurnstileWaitingRoomTask('https://example.com', 'key')
        ));

        $this->assertSame('TurnstileChallengeTaskProxyless', $challengePayload['task']['type']);
        $this->assertSame('TurnstileWaitingRoomTaskProxyless', $waitingRoomPayload['task']['type']);
    }

    public function testCreateTaskPayloadForComplexImageAndDataDome(): void
    {
        $complexPayload = $this->toPayload(new CreateTaskRequest(
            'API_KEY',
            new ComplexImageTask('BASE64_BODY', 'module')
        ));
        $dataDomePayload = $this->toPayload(new CreateTaskRequest(
            'API_KEY',
            new DataDomeTask('https://example.com', 'site-key', 'https://captcha.example.com')
        ));

        $this->assertSame('ComplexImageTask', $complexPayload['task']['type']);
        $this->assertSame('BASE64_BODY', $complexPayload['task']['body']);
        $this->assertSame('DataDomeTask', $dataDomePayload['task']['type']);
        $this->assertSame('https://captcha.example.com', $dataDomePayload['task']['captcha_url']);
    }

    /**
     * @return array<string, mixed>
     */
    private function toPayload(AbstractRequest $request): array
    {
        $json = (new RequestTransformer(new SerializerBuilder()))->transform($request);

        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }
}
