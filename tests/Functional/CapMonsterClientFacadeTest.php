<?php

declare(strict_types=1);

namespace Tests\Functional;

use CapMonsterClient\CapMonsterClient;
use CapMonsterClient\CapMonsterClientInterface;
use CapMonsterClient\CapMonsterConfiguration;
use CapMonsterClient\Common\Exception\CapMonsterException;
use CapMonsterClient\Enum\ErrorType;
use PHPUnit\Framework\TestCase;
use Tests\Support\RecordingHandler;
use Webclient\Fake\FakeHttpClient;

final class CapMonsterClientFacadeTest extends TestCase
{
    private RecordingHandler $handler;

    private CapMonsterClient $client;

    protected function setUp(): void
    {
        $this->handler = new RecordingHandler();
        $this->client = new CapMonsterClient(
            new FakeHttpClient($this->handler),
            new CapMonsterConfiguration('KEY')
        );
    }

    public function testImplementsPublicInterface(): void
    {
        self::assertInstanceOf(CapMonsterClientInterface::class, $this->client);
    }

    public function testGetBalance(): void
    {
        $this->handler->pushJson(['errorId' => 0, 'balance' => 12.34]);

        self::assertSame(12.34, $this->client->getBalance());
        self::assertSame('https://api.capmonster.cloud/getBalance', $this->handler->request(0)['uri']);
        self::assertSame('{"clientKey":"KEY"}', $this->handler->request(0)['body']);
    }

    public function testGetBalanceErrorMapsToTypedException(): void
    {
        $this->handler->pushJson(['errorId' => 1, 'errorCode' => 'ERROR_KEY_DOES_NOT_EXIST']);

        try {
            $this->client->getBalance();
            self::fail('Expected CapMonsterException was not thrown');
        } catch (CapMonsterException $exception) {
            self::assertSame(ErrorType::INVALID_KEY, $exception->getType());
        }
    }

    public function testGetActualUserAgent(): void
    {
        $this->handler->pushText("Mozilla/5.0 Test UA\n");

        self::assertSame('Mozilla/5.0 Test UA', $this->client->getActualUserAgent());
        self::assertSame('GET', $this->handler->request(0)['method']);
        self::assertSame('https://capmonster.cloud/api/useragent/actual', $this->handler->request(0)['uri']);
    }
}
