<?php

declare(strict_types=1);

namespace Tests\Unit;

use CapMonsterClient\CapMonsterConfiguration;
use CapMonsterClient\Common\Exception\EnumResolverException;
use CapMonsterClient\Enum\TypeTask;

final class CapMonsterConfigurationTest extends AbstractTestCase
{
    public function testDefaultBaseUrlMatchesCapMonsterCloudApi(): void
    {
        $configuration = new CapMonsterConfiguration(self::SECRET_KEY);

        $this->assertSame('https://api.capmonster.cloud', $configuration->getBaseUrl());
    }

    public function testCustomBaseUrlOverridesDefault(): void
    {
        $configuration = new CapMonsterConfiguration(self::SECRET_KEY, [
            'baseUrl' => 'https://staging-api.example.test',
        ]);

        $this->assertSame('https://staging-api.example.test', $configuration->getBaseUrl());
    }

    public function testCustomTimeoutOverridesDefaultForSameTaskType(): void
    {
        $configuration = new CapMonsterConfiguration(self::SECRET_KEY, [
            'timeouts' => [
                [
                    'taskType' => TypeTask::NO_CAPTCHA_TASK_PROXYLESS,
                    'firstRequestDelay' => 5,
                    'requestInterval' => 7,
                    'timeout' => 120,
                ],
            ],
        ]);

        $timeout = $configuration->getTimeoutConfig(TypeTask::NO_CAPTCHA_TASK_PROXYLESS);

        $this->assertSame(5, $timeout->getFirstRequestDelay());
        $this->assertSame(7, $timeout->getRequestInterval());
        $this->assertSame(120, $timeout->getTimeout());
    }

    public function testGetTimeoutConfigThrowsWhenTaskTypeNotConfigured(): void
    {
        $configuration = new CapMonsterConfiguration(self::SECRET_KEY, [
            'timeouts' => [
                [
                    'taskType' => TypeTask::NO_CAPTCHA_TASK_PROXYLESS,
                    'firstRequestDelay' => 0,
                    'requestInterval' => 0,
                    'timeout' => 10,
                ],
            ],
        ]);

        $this->expectException(EnumResolverException::class);
        $configuration->getTimeoutConfig(TypeTask::IMAGE_TO_TEXT_TASK);
    }
}
