<?php

declare(strict_types=1);

namespace Tests\Unit\Transformer;

use CapMonsterClient\ApiProvider\Request\Dto\CreateTaskRequest;
use CapMonsterClient\Dto\Task\NoCaptchaTask;
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
}
