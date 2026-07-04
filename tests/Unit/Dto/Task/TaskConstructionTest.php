<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Task;

use CapMonsterClient\Dto\Task\BinanceTask;
use CapMonsterClient\Dto\Task\FunCaptchaTask;
use CapMonsterClient\Dto\Task\GeeTestTask;
use CapMonsterClient\Dto\Task\HCaptchaTask;
use CapMonsterClient\Dto\Task\NoCaptchaTask;
use CapMonsterClient\Dto\Task\ProxySetting;
use CapMonsterClient\Dto\Task\RecaptchaV2EnterpriseTask;
use CapMonsterClient\Dto\Task\RecaptchaV3EnterpriseTask;
use CapMonsterClient\Dto\Task\TurnstileChallengeTask;
use CapMonsterClient\Dto\Task\TurnstileTask;
use CapMonsterClient\Dto\Task\TurnstileWaitingRoomTask;
use CapMonsterClient\Enum\TypeTask;
use PHPUnit\Framework\TestCase;

final class TaskConstructionTest extends TestCase
{
    private static function proxy(): ProxySetting
    {
        return ProxySetting::create('http', '1.2.3.4', 8080);
    }

    public function testNoCaptchaTaskTypeSwitchesOnProxy(): void
    {
        self::assertSame(
            TypeTask::NO_CAPTCHA_TASK_PROXYLESS,
            (new NoCaptchaTask('u', 'k'))->getType()
        );
        self::assertSame(
            TypeTask::NO_CAPTCHA_TASK,
            (new NoCaptchaTask('u', 'k', proxySetting: self::proxy()))->getType()
        );
    }

    public function testFunCaptchaTaskTypeSwitchesOnProxy(): void
    {
        self::assertSame(
            TypeTask::FUN_CAPTCHA_TASK_PROXYLESS,
            (new FunCaptchaTask('u', 'k'))->getType()
        );
        self::assertSame(
            TypeTask::FUN_CAPTCHA_TASK,
            (new FunCaptchaTask('u', 'k', proxySetting: self::proxy()))->getType()
        );
    }

    public function testHCaptchaTaskTypeSwitchesOnProxy(): void
    {
        self::assertSame(
            TypeTask::H_CAPTCHA_TASK_PROXYLESS,
            (new HCaptchaTask('u', 'k'))->getType()
        );
        self::assertSame(
            TypeTask::H_CAPTCHA_TASK,
            (new HCaptchaTask('u', 'k', proxySetting: self::proxy()))->getType()
        );
    }

    public function testGeeTestTaskTypeSwitchesOnProxy(): void
    {
        self::assertSame(
            TypeTask::GEE_TEST_TASK_PROXYLESS,
            (new GeeTestTask('u', 'gt'))->getType()
        );
        self::assertSame(
            TypeTask::GEE_TEST_TASK,
            (new GeeTestTask('u', 'gt', proxySetting: self::proxy()))->getType()
        );
    }

    public function testRecaptchaV2EnterpriseTaskTypeSwitchesOnProxy(): void
    {
        self::assertSame(
            TypeTask::RECAPTCHA_V2_ENTERPRISE_TASK_PROXYLESS,
            (new RecaptchaV2EnterpriseTask('u', 'k'))->getType()
        );
        self::assertSame(
            TypeTask::RECAPTCHA_V2_ENTERPRISE_TASK,
            (new RecaptchaV2EnterpriseTask('u', 'k', proxySetting: self::proxy()))->getType()
        );
    }

    public function testRecaptchaV3EnterpriseTaskAlwaysUsesNonProxylessType(): void
    {
        self::assertSame(
            TypeTask::RECAPTCHA_V3_ENTERPRISE_TASK,
            (new RecaptchaV3EnterpriseTask('u', 'k'))->getType()
        );
        self::assertSame(
            TypeTask::RECAPTCHA_V3_ENTERPRISE_TASK,
            (new RecaptchaV3EnterpriseTask('u', 'k', proxySetting: self::proxy()))->getType()
        );
    }

    public function testTurnstileVariantsUseDistinctEnumCases(): void
    {
        self::assertSame(TypeTask::TURNSTILE_TASK, (new TurnstileTask('u', 'k'))->getType());
        self::assertSame(
            TypeTask::TURNSTILE_CHALLENGE_TASK,
            (new TurnstileChallengeTask('u', 'k', 'token'))->getType()
        );
        self::assertSame(
            TypeTask::TURNSTILE_WAITING_ROOM_TASK,
            (new TurnstileWaitingRoomTask('u', 'k', 'html'))->getType()
        );
    }

    public function testAbstractTaskGettersAndFluentTaskId(): void
    {
        $proxy = self::proxy();
        $task = new BinanceTask('https://x.test/', 'sitekey', 'VALID', 'UA', 'c=1', $proxy);

        self::assertSame('https://x.test/', $task->getWebsiteUrl());
        self::assertSame('sitekey', $task->getWebsiteKey());
        self::assertSame('UA', $task->getUserAgent());
        self::assertSame('c=1', $task->getCookies());
        self::assertSame($proxy, $task->getProxySetting());
        self::assertSame('VALID', $task->getValidateId());
        self::assertSame(0, $task->getTaskId());
        self::assertSame($task, $task->setTaskId(55));
        self::assertSame(55, $task->getTaskId());
    }

    public function testProxySettingFactoryAndGetters(): void
    {
        $proxy = ProxySetting::create('socks4', '9.9.9.9', 4145, 'l', 'p');

        self::assertSame('socks4', $proxy->getProxyType());
        self::assertSame('9.9.9.9', $proxy->getProxyAddress());
        self::assertSame(4145, $proxy->getProxyPort());
        self::assertSame('l', $proxy->getProxyLogin());
        self::assertSame('p', $proxy->getProxyPassword());

        $anonymous = ProxySetting::create('http', '1.1.1.1', 80);
        self::assertNull($anonymous->getProxyLogin());
        self::assertNull($anonymous->getProxyPassword());
    }
}
