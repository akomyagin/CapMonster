<?php

declare(strict_types=1);

namespace Tests\Unit\Resolver;

use CapMonsterClient\Dto\Solution\GeeTestSolution;
use CapMonsterClient\Dto\Solution\HCaptchaSolution;
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
            'funcaptcha' => [TypeTask::FUN_CAPTCHA_TASK, TokenSolution::class],
            'funcaptcha_proxyless' => [TypeTask::FUN_CAPTCHA_TASK_PROXYLESS, TokenSolution::class],
            'turnstile' => [TypeTask::TURNSTILE_TASK, TokenSolution::class],
            'turnstile_proxyless' => [TypeTask::TURNSTILE_TASK_PROXYLESS, TokenSolution::class],
            'hcaptcha' => [TypeTask::H_CAPTCHA_TASK, HCaptchaSolution::class],
            'hcaptcha_proxyless' => [TypeTask::H_CAPTCHA_TASK_PROXYLESS, HCaptchaSolution::class],
            'geetest' => [TypeTask::GEE_TEST_TASK, GeeTestSolution::class],
            'geetest_proxyless' => [TypeTask::GEE_TEST_TASK_PROXYLESS, GeeTestSolution::class],
        ];
    }
}
