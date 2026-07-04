<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Task;

use CapMonsterClient\Dto\Task\DataDomeTask;
use CapMonsterClient\Dto\Task\FunCaptchaTask;
use CapMonsterClient\Dto\Task\NoCaptchaTask;
use CapMonsterClient\Dto\Task\ProxySetting;
use CapMonsterClient\Dto\Task\TurnstileTask;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Raw JMS toArray() shape of task DTOs, before RequestTransformer post-processing.
 */
final class TaskSerializationTest extends TestCase
{
    /**
     * @return array<string, mixed>
     */
    private function toArray(object $task): array
    {
        return (new SerializerBuilder())->build()->toArray($task);
    }

    public function testTypeIsEmittedAsVirtualPropertyFromEnumValue(): void
    {
        $array = $this->toArray(new TurnstileTask('https://x.test/', 'k'));

        self::assertSame('TurnstileTask', $array['type']);
    }

    public function testNoCaptchaRawTypeDependsOnProxyPresence(): void
    {
        $proxyless = $this->toArray(new NoCaptchaTask('https://x.test/', 'k'));
        $proxied = $this->toArray(new NoCaptchaTask(
            'https://x.test/',
            'k',
            proxySetting: ProxySetting::create('http', '1.2.3.4', 8080)
        ));

        self::assertSame('NoCaptchaTaskProxyless', $proxyless['type']);
        self::assertSame('NoCaptchaTask', $proxied['type']);
    }

    public function testCustomTaskOverridesTypeVirtualProperty(): void
    {
        $array = $this->toArray(new DataDomeTask('https://x.test/', 'k'));

        self::assertSame('CustomTask', $array['type']);
        self::assertSame('DataDome', $array['class']);
    }

    public function testProxySettingIsFlattenedIntoRootFields(): void
    {
        $array = $this->toArray(new TurnstileTask(
            'https://x.test/',
            'k',
            proxySetting: ProxySetting::create('socks5', '5.6.7.8', 1080, 'login', 'secret')
        ));

        self::assertSame('socks5', $array['proxyType']);
        self::assertSame('5.6.7.8', $array['proxyAddress']);
        self::assertSame(1080, $array['proxyPort']);
        self::assertSame('login', $array['proxyLogin']);
        self::assertSame('secret', $array['proxyPassword']);
        self::assertArrayNotHasKey('proxySetting', $array);
    }

    public function testProxyFieldsAreSkippedWithoutProxySetting(): void
    {
        $array = $this->toArray(new TurnstileTask('https://x.test/', 'k'));

        foreach (['proxyType', 'proxyAddress', 'proxyPort', 'proxyLogin', 'proxyPassword'] as $field) {
            self::assertArrayNotHasKey($field, $array);
        }
    }

    public function testTaskIdIsExcludedFromSerialization(): void
    {
        $task = new TurnstileTask('https://x.test/', 'k');
        $task->setTaskId(99);

        self::assertArrayNotHasKey('taskId', $this->toArray($task));
    }

    public function testWebsiteUrlUsesUpperCaseUrlWireName(): void
    {
        $array = $this->toArray(new TurnstileTask('https://x.test/', 'k'));

        self::assertSame('https://x.test/', $array['websiteURL']);
        self::assertArrayNotHasKey('websiteUrl', $array);
        self::assertArrayNotHasKey('website_url', $array);
    }

    public function testUserAgentAndCookiesAreSkippedWhenEmpty(): void
    {
        $array = $this->toArray(new TurnstileTask('https://x.test/', 'k'));

        self::assertArrayNotHasKey('userAgent', $array);
        self::assertArrayNotHasKey('cookies', $array);
    }

    public function testFunCaptchaRawSerializationUsesSnakeCaseVirtualNameForWebsitePublicKey(): void
    {
        // Raw JMS output: websiteKey mirrors the public key; RequestTransformer later rewrites it
        // to the documented "websitePublicKey" wire field.
        $array = $this->toArray(new FunCaptchaTask('https://x.test/', 'pubkey'));

        self::assertSame('pubkey', $array['websiteKey']);
    }
}
