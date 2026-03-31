<?php

declare(strict_types = 1);

namespace Tests\Unit;

use CapMonsterClient\CapMonsterClient;
use CapMonsterClient\Common\Exception\CapMonsterException;

final class CapMonsterBalanceTest extends AbstractTestCase
{
    private const BALANCE = 123.45;

    /**
     * @throws CapMonsterException
     */
    public function testBalance(): void
    {
        $client = new CapMonsterClient(
            $this->createHttpClient(['balance' => self::BALANCE]),
            $this->configuration
        );
        $balance = $client->getBalance();
        $this->assertSame(self::BALANCE, $balance);
    }

    public function testBalanceWithError(): void
    {
        $this->expectException(CapMonsterException::class);
        $client = new CapMonsterClient(
            $this->createHttpClient(['balance' => '99001']),
            $this->configuration
        );
        $client->getBalance();
    }

    public function testBalanceWithNotCode200(): void
    {
        $this->expectException(CapMonsterException::class);
        $client = new CapMonsterClient(
            $this->createHttpClient(['balance' => '98001']),
            $this->configuration
        );
        $client->getBalance();
    }
}