<?php

declare(strict_types=1);

namespace Tests\Unit\Resolver;

use CapMonsterClient\Dto\Solution\AbstractSolution;
use CapMonsterClient\Dto\Solution\GeeTestSolution;
use CapMonsterClient\Dto\Solution\HCaptchaSolution;
use CapMonsterClient\Dto\Solution\RawSolution;
use CapMonsterClient\Dto\Solution\ReCaptchaSolution;
use CapMonsterClient\Dto\Solution\TextSolution;
use CapMonsterClient\Dto\Solution\TokenSolution;
use CapMonsterClient\Enum\TypeTask;
use CapMonsterClient\Resolver\TypeSolutionResolver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Exhaustive: every TypeTask case must resolve without UnhandledMatchError
 * (regression guard for the removed TURNSTILE_CHALLENGE_TASK_PROXYLESS enum case).
 */
final class TypeSolutionResolverTest extends TestCase
{
    private const EXPECTED = [
        TypeTask::IMAGE_TO_TEXT_TASK->name => TextSolution::class,
        TypeTask::NO_CAPTCHA_TASK->name => ReCaptchaSolution::class,
        TypeTask::NO_CAPTCHA_TASK_PROXYLESS->name => ReCaptchaSolution::class,
        TypeTask::RECAPTCHA_V3_TASK_PROXYLESS->name => ReCaptchaSolution::class,
        TypeTask::RECAPTCHA_V2_ENTERPRISE_TASK->name => ReCaptchaSolution::class,
        TypeTask::RECAPTCHA_V2_ENTERPRISE_TASK_PROXYLESS->name => ReCaptchaSolution::class,
        TypeTask::RECAPTCHA_V3_ENTERPRISE_TASK->name => ReCaptchaSolution::class,
        TypeTask::RECAPTCHA_V3_ENTERPRISE_TASK_PROXYLESS->name => ReCaptchaSolution::class,
        TypeTask::FUN_CAPTCHA_TASK->name => TokenSolution::class,
        TypeTask::FUN_CAPTCHA_TASK_PROXYLESS->name => TokenSolution::class,
        TypeTask::H_CAPTCHA_TASK->name => HCaptchaSolution::class,
        TypeTask::H_CAPTCHA_TASK_PROXYLESS->name => HCaptchaSolution::class,
        TypeTask::GEE_TEST_TASK->name => GeeTestSolution::class,
        TypeTask::GEE_TEST_TASK_PROXYLESS->name => GeeTestSolution::class,
        TypeTask::TURNSTILE_TASK->name => TokenSolution::class,
        TypeTask::TURNSTILE_CHALLENGE_TASK->name => TokenSolution::class,
        TypeTask::TURNSTILE_WAITING_ROOM_TASK->name => TokenSolution::class,
        // {answer: string|array, metadata: {AnswerType}} varies per sub-recognizer → raw passthrough.
        TypeTask::COMPLEX_IMAGE_TASK->name => RawSolution::class,
        TypeTask::DATADOME_TASK->name => RawSolution::class,
        TypeTask::BASILISK_TASK->name => RawSolution::class,
        TypeTask::TENDI_TASK->name => RawSolution::class,
        TypeTask::AMAZON_TASK->name => RawSolution::class,
        TypeTask::BINANCE_TASK->name => RawSolution::class,
        TypeTask::IMPERVA_TASK->name => RawSolution::class,
        TypeTask::PROSOPO_TASK->name => RawSolution::class,
        TypeTask::YIDUN_TASK->name => RawSolution::class,
        TypeTask::MT_CAPTCHA_TASK->name => RawSolution::class,
        TypeTask::ALTCHA_TASK->name => RawSolution::class,
        TypeTask::CASTLE_TASK->name => RawSolution::class,
        TypeTask::TSPD_TASK->name => RawSolution::class,
        TypeTask::HUNT_TASK->name => RawSolution::class,
        TypeTask::ALIBABA_TASK->name => RawSolution::class,
    ];

    /**
     * @return iterable<string, array{TypeTask, class-string}>
     */
    public static function provideAllTypeTaskCases(): iterable
    {
        foreach (TypeTask::cases() as $case) {
            self::assertArrayHasKey(
                $case->name,
                self::EXPECTED,
                sprintf('Test map is missing TypeTask::%s — extend the expectations', $case->name)
            );
            yield $case->name => [$case, self::EXPECTED[$case->name]];
        }
    }

    /**
     * @param class-string $expectedClass
     */
    #[DataProvider('provideAllTypeTaskCases')]
    public function testResolvesEveryTypeTaskCase(TypeTask $typeTask, string $expectedClass): void
    {
        $resolved = (new TypeSolutionResolver())->resolve($typeTask);

        self::assertSame($expectedClass, $resolved);
        self::assertTrue(is_subclass_of($resolved, AbstractSolution::class));
    }

    public function testExpectationMapCoversExactlyAllEnumCases(): void
    {
        self::assertCount(count(TypeTask::cases()), self::EXPECTED);
    }
}
