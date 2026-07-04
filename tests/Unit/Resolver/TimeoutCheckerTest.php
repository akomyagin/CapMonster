<?php

declare(strict_types=1);

namespace Tests\Unit\Resolver;

use CapMonsterClient\Common\Exception\CapMonsterException;
use CapMonsterClient\Dto\Config\TimeoutConfig;
use CapMonsterClient\Enum\ErrorType;
use CapMonsterClient\Enum\TypeTask;
use CapMonsterClient\Resolver\TimeoutChecker;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class TimeoutCheckerTest extends TestCase
{
    private static function config(int $firstRequestDelay, int $requestInterval, int $timeout): TimeoutConfig
    {
        return new TimeoutConfig(TypeTask::TURNSTILE_TASK, $firstRequestDelay, $requestInterval, $timeout);
    }

    public function testFirstCallReturnsStartPlusFirstRequestDelay(): void
    {
        $start = new DateTimeImmutable('2026-01-01 00:00:00');

        $result = (new TimeoutChecker())->resolve(self::config(0, 0, 10), $start);

        self::assertEquals($start, $result);
    }

    public function testFirstCallWithNonZeroDelayAdvancesTimestamp(): void
    {
        $start = new DateTimeImmutable('2026-01-01 00:00:00');

        // firstRequestDelay=1 → one real second of sleep, still fast enough for a unit test
        $result = (new TimeoutChecker())->resolve(self::config(1, 0, 10), $start);

        self::assertEquals($start->modify('+1 seconds'), $result);
    }

    public function testSubsequentCallAdvancesByRequestInterval(): void
    {
        $start = new DateTimeImmutable('2026-01-01 00:00:00');
        $current = $start->modify('+4 seconds');

        $result = (new TimeoutChecker())->resolve(self::config(0, 0, 100), $start, $current);

        self::assertEquals($current, $result);
    }

    public function testThrowsTimeoutExpiredWhenBudgetIsExceeded(): void
    {
        $start = new DateTimeImmutable('2026-01-01 00:00:00');
        // current + interval(1) > start + timeout(0) → must throw before sleeping
        $checker = new TimeoutChecker();

        try {
            $checker->resolve(self::config(0, 1, 0), $start, $start);
            self::fail('Expected CapMonsterException was not thrown');
        } catch (CapMonsterException $exception) {
            self::assertSame(ErrorType::TIMEOUT_EXPIRED, $exception->getType());
            self::assertSame(ErrorType::TIMEOUT_EXPIRED->description(), $exception->getMessage());
        }
    }

    public function testBoundaryIsInclusiveCurrentPlusIntervalEqualToDeadlineDoesNotThrow(): void
    {
        $start = new DateTimeImmutable('2026-01-01 00:00:00');

        // current + 0 == start + 0 → not strictly greater → allowed
        $result = (new TimeoutChecker())->resolve(self::config(0, 0, 0), $start, $start);

        self::assertEquals($start, $result);
    }

    public function testThrowsOnceIntervalStepsPastDeadline(): void
    {
        $start = new DateTimeImmutable('2026-01-01 00:00:00');
        $current = $start->modify('+10 seconds');

        $this->expectException(CapMonsterException::class);
        (new TimeoutChecker())->resolve(self::config(0, 1, 10), $start, $current);
    }
}
