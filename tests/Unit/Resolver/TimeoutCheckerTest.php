<?php

declare(strict_types=1);

namespace Tests\Unit\Resolver;

use CapMonsterClient\Common\Exception\CapMonsterException;
use CapMonsterClient\Dto\Config\TimeoutConfig;
use CapMonsterClient\Enum\TypeTask;
use CapMonsterClient\Resolver\TimeoutChecker;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class TimeoutCheckerTest extends TestCase
{
    public function testResolveFirstIterationWithZeroDelay(): void
    {
        $checker = new TimeoutChecker();
        $start = new DateTimeImmutable();
        $timeout = new TimeoutConfig(TypeTask::NO_CAPTCHA_TASK_PROXYLESS, 0, 0, 5);

        $current = $checker->resolve($timeout, $start);

        $this->assertGreaterThanOrEqual($start->getTimestamp(), $current->getTimestamp());
    }

    public function testResolveThrowsWhenTimeoutExceeded(): void
    {
        $checker = new TimeoutChecker();
        $start = new DateTimeImmutable('-10 seconds');
        $current = new DateTimeImmutable('-6 seconds');
        $timeout = new TimeoutConfig(TypeTask::NO_CAPTCHA_TASK_PROXYLESS, 0, 1, 1);

        $this->expectException(CapMonsterException::class);
        $checker->resolve($timeout, $start, $current);
    }
}
