<?php

declare(strict_types=1);

namespace Tests\Unit\Resolver;

use CapMonsterClient\Dto\Solution\GeeTestSolution;
use CapMonsterClient\Dto\Solution\HCaptchaSolution;
use CapMonsterClient\Dto\Solution\RawSolution;
use CapMonsterClient\Dto\Solution\ReCaptchaSolution;
use CapMonsterClient\Dto\Solution\TextSolution;
use CapMonsterClient\Dto\Solution\TokenSolution;
use CapMonsterClient\Enum\TypeTask;
use CapMonsterClient\Resolver\TypeSolutionResolver;
use PHPUnit\Framework\TestCase;

final class TypeSolutionResolverTest extends TestCase
{
    /**
     * @dataProvider provideTypes
     */
    public function testResolve(TypeTask $typeTask, string $expectedSolutionClass): void
    {
        $resolver = new TypeSolutionResolver();

        $actual = $resolver->resolve($typeTask);

        $this->assertSame($expectedSolutionClass, $actual);
    }

    /**
     * @return array<string, array{0: TypeTask, 1: string}>
     */
    public static function provideTypes(): array
    {
        return [
            'image' => [TypeTask::IMAGE_TO_TEXT_TASK, TextSolution::class],
            'no_captcha' => [TypeTask::NO_CAPTCHA_TASK, ReCaptchaSolution::class],
            'no_captcha_proxyless' => [TypeTask::NO_CAPTCHA_TASK_PROXYLESS, ReCaptchaSolution::class],
            'recaptcha_v3_proxyless' => [TypeTask::RECAPTCHA_V3_TASK_PROXYLESS, ReCaptchaSolution::class],
            'recaptcha_v2_enterprise' => [TypeTask::RECAPTCHA_V2_ENTERPRISE_TASK, ReCaptchaSolution::class],
            'recaptcha_v2_enterprise_proxyless' => [TypeTask::RECAPTCHA_V2_ENTERPRISE_TASK_PROXYLESS, ReCaptchaSolution::class],
            'recaptcha_v3_enterprise' => [TypeTask::RECAPTCHA_V3_ENTERPRISE_TASK, ReCaptchaSolution::class],
            'recaptcha_v3_enterprise_proxyless' => [TypeTask::RECAPTCHA_V3_ENTERPRISE_TASK_PROXYLESS, ReCaptchaSolution::class],
            'funcaptcha' => [TypeTask::FUN_CAPTCHA_TASK, TokenSolution::class],
            'funcaptcha_proxyless' => [TypeTask::FUN_CAPTCHA_TASK_PROXYLESS, TokenSolution::class],
            'turnstile' => [TypeTask::TURNSTILE_TASK, TokenSolution::class],
            'turnstile_proxyless' => [TypeTask::TURNSTILE_TASK_PROXYLESS, TokenSolution::class],
            'turnstile_challenge' => [TypeTask::TURNSTILE_CHALLENGE_TASK, TokenSolution::class],
            'turnstile_challenge_proxyless' => [TypeTask::TURNSTILE_CHALLENGE_TASK_PROXYLESS, TokenSolution::class],
            'turnstile_waiting_room' => [TypeTask::TURNSTILE_WAITING_ROOM_TASK, TokenSolution::class],
            'turnstile_waiting_room_proxyless' => [TypeTask::TURNSTILE_WAITING_ROOM_TASK_PROXYLESS, TokenSolution::class],
            'hcaptcha' => [TypeTask::H_CAPTCHA_TASK, HCaptchaSolution::class],
            'hcaptcha_proxyless' => [TypeTask::H_CAPTCHA_TASK_PROXYLESS, HCaptchaSolution::class],
            'geetest' => [TypeTask::GEE_TEST_TASK, GeeTestSolution::class],
            'geetest_proxyless' => [TypeTask::GEE_TEST_TASK_PROXYLESS, GeeTestSolution::class],
            'complex_image' => [TypeTask::COMPLEX_IMAGE_TASK, TextSolution::class],
            'datadome' => [TypeTask::DATADOME_TASK, RawSolution::class],
            'basilisk' => [TypeTask::BASILISK_TASK, RawSolution::class],
            'tendi' => [TypeTask::TENDI_TASK, RawSolution::class],
            'amazon' => [TypeTask::AMAZON_TASK, RawSolution::class],
            'binance' => [TypeTask::BINANCE_TASK, RawSolution::class],
            'imperva' => [TypeTask::IMPERVA_TASK, RawSolution::class],
            'prosopo' => [TypeTask::PROSOPO_TASK, RawSolution::class],
            'yidun' => [TypeTask::YIDUN_TASK, RawSolution::class],
            'mtcaptcha' => [TypeTask::MT_CAPTCHA_TASK, RawSolution::class],
            'altcha' => [TypeTask::ALTCHA_TASK, RawSolution::class],
            'castle' => [TypeTask::CASTLE_TASK, RawSolution::class],
            'tspd' => [TypeTask::TSPD_TASK, RawSolution::class],
            'hunt' => [TypeTask::HUNT_TASK, RawSolution::class],
            'alibaba' => [TypeTask::ALIBABA_TASK, RawSolution::class],
        ];
    }
}
