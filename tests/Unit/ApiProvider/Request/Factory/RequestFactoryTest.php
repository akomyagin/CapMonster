<?php

declare(strict_types=1);

namespace Tests\Unit\ApiProvider\Request\Factory;

use CapMonsterClient\ApiProvider\Request\Dto\CreateTaskRequest;
use CapMonsterClient\ApiProvider\Request\Dto\GetBalanceRequest;
use CapMonsterClient\ApiProvider\Request\Dto\GetTaskResultRequest;
use CapMonsterClient\ApiProvider\Request\Factory\RequestFactory;
use CapMonsterClient\CapMonsterConfiguration;
use CapMonsterClient\Dto\Task\ImageToTextTask;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

final class RequestFactoryTest extends TestCase
{
    private function factory(CapMonsterConfiguration $configuration): RequestFactory
    {
        return new RequestFactory(new SerializerBuilder(), $configuration);
    }

    public function testGetBalanceRequestHasCorrectMethodUriAndBody(): void
    {
        $request = $this->factory(new CapMonsterConfiguration('KEY'))->create(new GetBalanceRequest('KEY'));

        self::assertInstanceOf(RequestInterface::class, $request);
        self::assertSame('POST', $request->getMethod());
        self::assertSame('https://api.capmonster.cloud/getBalance', (string) $request->getUri());
        self::assertSame('{"clientKey":"KEY"}', (string) $request->getBody());
    }

    public function testDefaultHeadersAreActuallyAttachedToTheRequest(): void
    {
        // Regression guard: header attachment used to call withHeader() on the PSR-17
        // factory itself, which fataled; headers must land on the built request.
        $request = $this->factory(new CapMonsterConfiguration('KEY'))->create(new GetBalanceRequest('KEY'));

        self::assertSame('application/json', $request->getHeaderLine('Content-Type'));
        self::assertSame('application/json', $request->getHeaderLine('Accept'));
    }

    public function testCustomHeadersIncludingMultiValueAreAttached(): void
    {
        $configuration = new CapMonsterConfiguration('KEY', [
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Custom' => 'v1',
                'X-Multi' => ['a', 'b'],
            ],
        ]);

        $request = $this->factory($configuration)->create(new GetBalanceRequest('KEY'));

        self::assertSame('v1', $request->getHeaderLine('X-Custom'));
        self::assertSame(['a', 'b'], $request->getHeader('X-Multi'));
    }

    public function testCreateTaskRequestTargetsCreateTaskEndpoint(): void
    {
        $request = $this->factory(new CapMonsterConfiguration('KEY'))->create(
            new CreateTaskRequest('KEY', new ImageToTextTask('B64'))
        );

        self::assertSame('https://api.capmonster.cloud/createTask', (string) $request->getUri());
        self::assertSame('{"clientKey":"KEY","task":{"type":"ImageToTextTask","body":"B64"}}', (string) $request->getBody());
    }

    public function testGetTaskResultRequestTargetsGetTaskResultEndpoint(): void
    {
        $task = new ImageToTextTask('B64');
        $task->setTaskId(11);

        $request = $this->factory(new CapMonsterConfiguration('KEY'))->create(new GetTaskResultRequest('KEY', $task));

        self::assertSame('https://api.capmonster.cloud/getTaskResult', (string) $request->getUri());
        self::assertSame('{"clientKey":"KEY","taskId":11}', (string) $request->getBody());
    }

    public function testCustomBaseUrlIsUsed(): void
    {
        $configuration = new CapMonsterConfiguration('KEY', ['baseUrl' => 'https://mirror.test']);

        $request = $this->factory($configuration)->create(new GetBalanceRequest('KEY'));

        self::assertSame('https://mirror.test/getBalance', (string) $request->getUri());
    }
}
