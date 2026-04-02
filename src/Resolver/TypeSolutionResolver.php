<?php

declare(strict_types=1);

namespace CapMonsterClient\Resolver;

use CapMonsterClient\Dto\Solution\GeeTestSolution;
use CapMonsterClient\Dto\Solution\HCaptchaSolution;
use CapMonsterClient\Dto\Solution\RawSolution;
use CapMonsterClient\Dto\Solution\ReCaptchaSolution;
use CapMonsterClient\Dto\Solution\TextSolution;
use CapMonsterClient\Dto\Solution\TokenSolution;
use CapMonsterClient\Enum\TypeTask;

final class TypeSolutionResolver
{
    public function resolve(TypeTask $typeTask): string
    {
        return match ($typeTask) {
            TypeTask::IMAGE_TO_TEXT_TASK => TextSolution::class,
            TypeTask::NO_CAPTCHA_TASK, TypeTask::NO_CAPTCHA_TASK_PROXYLESS, TypeTask::RECAPTCHA_V3_TASK_PROXYLESS,
            TypeTask::RECAPTCHA_V2_ENTERPRISE_TASK, TypeTask::RECAPTCHA_V2_ENTERPRISE_TASK_PROXYLESS,
            TypeTask::RECAPTCHA_V3_ENTERPRISE_TASK, TypeTask::RECAPTCHA_V3_ENTERPRISE_TASK_PROXYLESS => ReCaptchaSolution::class,
            TypeTask::FUN_CAPTCHA_TASK, TypeTask::FUN_CAPTCHA_TASK_PROXYLESS,
            TypeTask::TURNSTILE_TASK, TypeTask::TURNSTILE_TASK_PROXYLESS,
            TypeTask::TURNSTILE_CHALLENGE_TASK, TypeTask::TURNSTILE_CHALLENGE_TASK_PROXYLESS,
            TypeTask::TURNSTILE_WAITING_ROOM_TASK, TypeTask::TURNSTILE_WAITING_ROOM_TASK_PROXYLESS => TokenSolution::class,
            TypeTask::H_CAPTCHA_TASK, TypeTask::H_CAPTCHA_TASK_PROXYLESS => HCaptchaSolution::class,
            TypeTask::GEE_TEST_TASK_PROXYLESS, TypeTask::GEE_TEST_TASK => GeeTestSolution::class,
            TypeTask::COMPLEX_IMAGE_TASK => TextSolution::class,
            TypeTask::DATADOME_TASK, TypeTask::BASILISK_TASK, TypeTask::TENDI_TASK, TypeTask::AMAZON_TASK,
            TypeTask::BINANCE_TASK, TypeTask::IMPERVA_TASK, TypeTask::PROSOPO_TASK, TypeTask::YIDUN_TASK,
            TypeTask::MT_CAPTCHA_TASK, TypeTask::ALTCHA_TASK, TypeTask::CASTLE_TASK, TypeTask::TSPD_TASK,
            TypeTask::HUNT_TASK, TypeTask::ALIBABA_TASK => RawSolution::class,
        };
    }
}