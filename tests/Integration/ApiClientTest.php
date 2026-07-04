<?php

declare(strict_types=1);

namespace Tests\Integration;

use CapMonsterClient\ApiProvider\ApiClient;
use CapMonsterClient\CapMonsterConfiguration;
use CapMonsterClient\Dto\Task\AbstractTask;
use CapMonsterClient\Dto\Task\BinanceTask;
use CapMonsterClient\Dto\Task\DataDomeTask;
use CapMonsterClient\Dto\Task\ImageToTextTask;
use CapMonsterClient\Dto\Task\NoCaptchaTask;
use CapMonsterClient\Dto\Task\ProxySetting;
use CapMonsterClient\Dto\Task\TurnstileTask;
use CapMonsterClient\Dto\Task\TurnstileWaitingRoomTask;
use CapMonsterClient\Enum\StatusTask;
use PHPUnit\Framework\TestCase;
use Tests\Support\RecordingHandler;
use Webclient\Fake\FakeHttpClient;

/**
 * ApiClient end-to-end against a scripted PSR-18 transport: real RequestFactory,
 * real RequestTransformer, real JMS (de)serialization. The recorded request bodies
 * are asserted byte-for-byte — this pins the wire format of the create-task payloads.
 */
final class ApiClientTest extends TestCase
{
    private RecordingHandler $handler;

    private ApiClient $apiClient;

    protected function setUp(): void
    {
        $this->handler = new RecordingHandler();
        $this->apiClient = new ApiClient(
            new FakeHttpClient($this->handler),
            new CapMonsterConfiguration('KEY')
        );
    }

    private static function proxy(): ProxySetting
    {
        return ProxySetting::create('http', '1.2.3.4', 8080, 'user', 'pass');
    }

    private function createTaskAndCaptureBody(AbstractTask $task): string
    {
        $this->handler->pushJson(['errorId' => 0, 'taskId' => 1]);
        $this->apiClient->createTask($task);

        $request = $this->handler->request(0);
        self::assertSame('POST', $request['method']);
        self::assertSame('https://api.capmonster.cloud/createTask', $request['uri']);

        return $request['body'];
    }

    // ------------------------------------------------------------------ createTask success + wire bytes

    public function testCreateTaskReturnsTaskIdAndSendsJsonHeaders(): void
    {
        $this->handler->pushJson(['errorId' => 0, 'taskId' => 987]);

        $response = $this->apiClient->createTask(new ImageToTextTask('B64'));

        self::assertSame(987, $response->getTaskId());
        self::assertFalse($response->isError());
        $request = $this->handler->request(0);
        self::assertSame(['application/json'], $request['headers']['Content-Type']);
        self::assertSame(['application/json'], $request['headers']['Accept']);
    }

    public function testImageToTextTaskWireBytes(): void
    {
        $body = $this->createTaskAndCaptureBody(new ImageToTextTask('B64'));

        self::assertSame('{"clientKey":"KEY","task":{"type":"ImageToTextTask","body":"B64"}}', $body);
    }

    public function testNoCaptchaTaskProxylessWireBytes(): void
    {
        $body = $this->createTaskAndCaptureBody(
            new NoCaptchaTask('https://x.test/', 'sitekey', 'dataS', true, 'UA', 'a=b')
        );

        self::assertSame(
            '{"clientKey":"KEY","task":{"type":"RecaptchaV2TaskProxyless",'
            . '"websiteURL":"https:\/\/x.test\/","websiteKey":"sitekey","userAgent":"UA","cookies":"a=b",'
            . '"recaptchaDataSValue":"dataS","isInvisible":true}}',
            $body
        );
    }

    public function testBinanceTaskWireBytes(): void
    {
        $body = $this->createTaskAndCaptureBody(
            new BinanceTask('https://x.test/', 'sitekey', 'VALID', 'UA')
        );

        self::assertSame(
            '{"clientKey":"KEY","task":{"type":"BinanceTask",'
            . '"websiteURL":"https:\/\/x.test\/","websiteKey":"sitekey","userAgent":"UA","validateId":"VALID"}}',
            $body
        );
    }

    public function testTurnstileTaskWithProxyWireBytes(): void
    {
        $body = $this->createTaskAndCaptureBody(
            new TurnstileTask('https://x.test/', 'sitekey', proxySetting: self::proxy())
        );

        self::assertSame(
            '{"clientKey":"KEY","task":{"type":"TurnstileTask",'
            . '"proxyType":"http","proxyAddress":"1.2.3.4","proxyPort":8080,"proxyLogin":"user","proxyPassword":"pass",'
            . '"websiteURL":"https:\/\/x.test\/","websiteKey":"sitekey"}}',
            $body
        );
    }

    public function testTurnstileWaitingRoomTaskWireBytes(): void
    {
        $body = $this->createTaskAndCaptureBody(
            new TurnstileWaitingRoomTask('https://x.test/', 'sitekey', 'html64', 'UA')
        );

        self::assertSame(
            '{"clientKey":"KEY","task":{"type":"TurnstileTask",'
            . '"websiteURL":"https:\/\/x.test\/","websiteKey":"sitekey","userAgent":"UA",'
            . '"cloudflareTaskType":"wait_room","htmlPageBase64":"html64"}}',
            $body
        );
    }

    public function testDataDomeTaskWireBytes(): void
    {
        $body = $this->createTaskAndCaptureBody(new DataDomeTask(
            'https://x.test/',
            'ignored-key',
            'https://cap.url/',
            '{"datadomeCookie":"dd=1"}',
            'UA',
            'c=1',
            self::proxy()
        ));

        self::assertSame(
            '{"clientKey":"KEY","task":{"type":"CustomTask",'
            . '"proxyType":"http","proxyAddress":"1.2.3.4","proxyPort":8080,"proxyLogin":"user","proxyPassword":"pass",'
            . '"websiteURL":"https:\/\/x.test\/","userAgent":"UA","class":"DataDome",'
            . '"metadata":{"datadomeCookie":"dd=1","captchaUrl":"https:\/\/cap.url\/"}}}',
            $body
        );
    }

    public function testCreateTaskWithCallbackUrlFromConfiguration(): void
    {
        $handler = new RecordingHandler();
        $handler->pushJson(['errorId' => 0, 'taskId' => 5]);
        $apiClient = new ApiClient(
            new FakeHttpClient($handler),
            new CapMonsterConfiguration('KEY', ['callbackUrl' => 'https://cb.test/hook'])
        );

        $apiClient->createTask(new ImageToTextTask('B64'));

        self::assertSame(
            '{"clientKey":"KEY","task":{"type":"ImageToTextTask","body":"B64"},"callbackUrl":"https:\/\/cb.test\/hook"}',
            $handler->request(0)['body']
        );
    }

    // ------------------------------------------------------------------ getTaskResult

    public function testGetResultTaskSendsTaskIdAndParsesProcessing(): void
    {
        $this->handler->pushJson(['errorId' => 0, 'status' => 'processing']);
        $task = new ImageToTextTask('B64');
        $task->setTaskId(321);

        $response = $this->apiClient->getResultTask($task);

        self::assertSame(StatusTask::PROCESSING, $response->getStatus());
        $request = $this->handler->request(0);
        self::assertSame('https://api.capmonster.cloud/getTaskResult', $request['uri']);
        self::assertSame('{"clientKey":"KEY","taskId":321}', $request['body']);
    }

    public function testGetResultTaskParsesReadySolution(): void
    {
        $this->handler->pushJson([
            'errorId' => 0,
            'status' => 'ready',
            'solution' => ['token' => 'TKN', 'userAgent' => 'UA'],
        ]);
        $task = new TurnstileTask('https://x.test/', 'k');
        $task->setTaskId(1);

        $response = $this->apiClient->getResultTask($task);

        self::assertSame(StatusTask::READY, $response->getStatus());
        self::assertSame(['token' => 'TKN', 'userAgent' => 'UA'], $response->getSolution());
    }

    // ------------------------------------------------------------------ getBalance

    public function testGetBalanceSuccess(): void
    {
        $this->handler->pushJson(['errorId' => 0, 'balance' => 345.67]);

        self::assertSame(345.67, $this->apiClient->getBalance());
        $request = $this->handler->request(0);
        self::assertSame('https://api.capmonster.cloud/getBalance', $request['uri']);
        self::assertSame('{"clientKey":"KEY"}', $request['body']);
    }
}
