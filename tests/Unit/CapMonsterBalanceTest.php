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
        $client = new CapMonsterClient(
            $this->createHttpClient(['balance' => (99000 + rand(100, 999))/100]),
            $this->configuration
        );
        $balance = $client->getBalance();
        $this->assertSame(self::BALANCE, $balance);
    }

    public function testBalanceWithNotCode200(): void
    {
        $client = new CapMonsterClient(
            $this->createHttpClient(['balance' => (98000 + rand(100, 999))/100]),
            $this->configuration
        );
        $balance = $client->getBalance();
        $this->assertSame(self::BALANCE, $balance);
    }
}