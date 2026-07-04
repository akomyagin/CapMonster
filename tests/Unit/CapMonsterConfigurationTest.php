<?php

declare(strict_types=1);

namespace Tests\Unit;

use CapMonsterClient\CapMonsterConfiguration;
use CapMonsterClient\Enum\TypeTask;
use PHPUnit\Framework\TestCase;
use ValueError;

final class CapMonsterConfigurationTest extends TestCase
{
    public function testDefaults(): void
    {
        $config = new CapMonsterConfiguration('client-key');

        self::assertSame('client-key', $config->getClientKey());
        self::assertSame('https://api.capmonster.cloud', $config->getBaseUrl());
        self::assertSame('POST', $config->getMethod());
        self::assertSame(120, $config->getMaxGetTaskResultAttempts());
        self::assertNull($config->getCallbackUrl());
        self::assertSame(
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            $config->getHeaders()
        );
    }

    public function testDefaultTimeoutsExistForEveryTypeTask(): void
    {
        $config = new CapMonsterConfiguration('key');

        foreach (TypeTask::cases() as $typeTask) {
            $timeout = $config->getTimeoutConfig($typeTask);
            self::assertSame($typeTask, $timeout->getTypeTask());
            self::assertSame(2, $timeout->getFirstRequestDelay());
            self::assertSame(2, $timeout->getRequestInterval());
            self::assertSame(120, $timeout->getTimeout());
        }
    }

    public function testCustomTimeoutOverridesDefaultOnlyForItsType(): void
    {
        $config = new CapMonsterConfiguration('key', [
            'timeouts' => [
                [
                    'taskType' => TypeTask::IMAGE_TO_TEXT_TASK,
                    'firstRequestDelay' => 0,
                    'requestInterval' => 1,
                    'timeout' => 5,
                ],
            ],
        ]);

        $custom = $config->getTimeoutConfig(TypeTask::IMAGE_TO_TEXT_TASK);
        self::assertSame(0, $custom->getFirstRequestDelay());
        self::assertSame(1, $custom->getRequestInterval());
        self::assertSame(5, $custom->getTimeout());

        $untouched = $config->getTimeoutConfig(TypeTask::TURNSTILE_TASK);
        self::assertSame(2, $untouched->getFirstRequestDelay());
        self::assertSame(2, $untouched->getRequestInterval());
        self::assertSame(120, $untouched->getTimeout());
    }

    public function testCustomTimeoutAcceptsStringTaskType(): void
    {
        $config = new CapMonsterConfiguration('key', [
            'timeouts' => [
                [
                    'taskType' => 'TurnstileTask',
                    'firstRequestDelay' => 3,
                    'requestInterval' => 4,
                    'timeout' => 30,
                ],
            ],
        ]);

        $timeout = $config->getTimeoutConfig(TypeTask::TURNSTILE_TASK);
        self::assertSame(3, $timeout->getFirstRequestDelay());
        self::assertSame(4, $timeout->getRequestInterval());
        self::assertSame(30, $timeout->getTimeout());
    }

    public function testUnknownTimeoutTaskTypeStringThrows(): void
    {
        $this->expectException(ValueError::class);

        new CapMonsterConfiguration('key', [
            'timeouts' => [
                [
                    'taskType' => 'NotATask',
                    'firstRequestDelay' => 0,
                    'requestInterval' => 1,
                    'timeout' => 5,
                ],
            ],
        ]);
    }

    public function testCustomHeadersReplaceDefaultsEntirely(): void
    {
        // The merge is a top-level array_merge: a custom "headers" entry replaces
        // the whole default header set (Content-Type/Accept are NOT kept).
        $config = new CapMonsterConfiguration('key', ['headers' => ['X-Custom' => 'v']]);

        self::assertSame(['X-Custom' => 'v'], $config->getHeaders());
    }

    public function testScalarOverrides(): void
    {
        $config = new CapMonsterConfiguration('key', [
            'baseUrl' => 'https://proxy.internal',
            'method' => 'POST',
            'maxGetTaskResultAttempts' => 3,
            'callbackUrl' => 'https://cb.test/hook',
        ]);

        self::assertSame('https://proxy.internal', $config->getBaseUrl());
        self::assertSame(3, $config->getMaxGetTaskResultAttempts());
        self::assertSame('https://cb.test/hook', $config->getCallbackUrl());
    }
}
