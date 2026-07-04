<?php

declare(strict_types=1);

namespace Tests\Unit\Enum;

use CapMonsterClient\Common\Exception\EnumResolverException;
use CapMonsterClient\Enum\StatusTask;
use PHPUnit\Framework\TestCase;

final class StatusTaskTest extends TestCase
{
    public function testResolveKnownStatuses(): void
    {
        self::assertSame(StatusTask::PROCESSING, StatusTask::resolve('processing'));
        self::assertSame(StatusTask::READY, StatusTask::resolve('ready'));
    }

    public function testResolveUnknownStatusThrows(): void
    {
        $this->expectException(EnumResolverException::class);
        $this->expectExceptionMessage('Status pending is not resolve in StatusTask enum');

        StatusTask::resolve('pending');
    }

    public function testDescriptionsAreDefinedForAllCases(): void
    {
        foreach (StatusTask::cases() as $case) {
            self::assertNotSame('', $case->description());
        }
    }
}
