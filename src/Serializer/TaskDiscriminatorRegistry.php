<?php

declare(strict_types=1);

namespace CapMonsterClient\Serializer;

use CapMonsterClient\Dto\Task\AlibabaTask;
use CapMonsterClient\Dto\Task\AltchaTask;
use CapMonsterClient\Dto\Task\AmazonTask;
use CapMonsterClient\Dto\Task\BasiliskTask;
use CapMonsterClient\Dto\Task\BinanceTask;
use CapMonsterClient\Dto\Task\CastleTask;
use CapMonsterClient\Dto\Task\ComplexImageTask;
use CapMonsterClient\Dto\Task\DataDomeTask;
use CapMonsterClient\Dto\Task\FunCaptchaTask;
use CapMonsterClient\Dto\Task\GeeTestTask;
use CapMonsterClient\Dto\Task\HCaptchaTask;
use CapMonsterClient\Dto\Task\HuntTask;
use CapMonsterClient\Dto\Task\ImageToTextTask;
use CapMonsterClient\Dto\Task\ImpervaTask;
use CapMonsterClient\Dto\Task\MTCaptchaTask;
use CapMonsterClient\Dto\Task\NoCaptchaTask;
use CapMonsterClient\Dto\Task\ProsopoTask;
use CapMonsterClient\Dto\Task\RecaptchaV2EnterpriseTask;
use CapMonsterClient\Dto\Task\RecaptchaV3EnterpriseTask;
use CapMonsterClient\Dto\Task\RecaptchaV3TaskProxyless;
use CapMonsterClient\Dto\Task\TenDITask;
use CapMonsterClient\Dto\Task\TSPDTask;
use CapMonsterClient\Dto\Task\TurnstileChallengeTask;
use CapMonsterClient\Dto\Task\TurnstileTask;
use CapMonsterClient\Dto\Task\TurnstileWaitingRoomTask;
use CapMonsterClient\Dto\Task\YidunTask;
use CapMonsterClient\Enum\TypeTask;

/**
 * Maps API / enum task "type" string to the PHP task class.
 *
 * JMS #[Discriminator] is not used on AbstractTask: multiple TypeTask values map to the same PHP class
 * (e.g. proxy vs proxyless), and CustomTask wire types use a different JSON "type" than the PHP enum.
 *
 * @phpstan-type DiscriminatorMap array<string, class-string>
 */
final class TaskDiscriminatorRegistry
{
    /**
     * Wire JSON field `task.type` (and PHP TypeTask::value) → concrete task class.
     *
     * @var DiscriminatorMap
     */
    public const TYPE_TO_CLASS = [
        TypeTask::IMAGE_TO_TEXT_TASK->value => ImageToTextTask::class,
        TypeTask::NO_CAPTCHA_TASK->value => NoCaptchaTask::class,
        TypeTask::NO_CAPTCHA_TASK_PROXYLESS->value => NoCaptchaTask::class,
        TypeTask::RECAPTCHA_V3_TASK_PROXYLESS->value => RecaptchaV3TaskProxyless::class,
        TypeTask::RECAPTCHA_V2_ENTERPRISE_TASK->value => RecaptchaV2EnterpriseTask::class,
        TypeTask::RECAPTCHA_V2_ENTERPRISE_TASK_PROXYLESS->value => RecaptchaV2EnterpriseTask::class,
        TypeTask::RECAPTCHA_V3_ENTERPRISE_TASK->value => RecaptchaV3EnterpriseTask::class,
        TypeTask::RECAPTCHA_V3_ENTERPRISE_TASK_PROXYLESS->value => RecaptchaV3EnterpriseTask::class,
        TypeTask::FUN_CAPTCHA_TASK->value => FunCaptchaTask::class,
        TypeTask::FUN_CAPTCHA_TASK_PROXYLESS->value => FunCaptchaTask::class,
        TypeTask::H_CAPTCHA_TASK->value => HCaptchaTask::class,
        TypeTask::H_CAPTCHA_TASK_PROXYLESS->value => HCaptchaTask::class,
        TypeTask::GEE_TEST_TASK->value => GeeTestTask::class,
        TypeTask::GEE_TEST_TASK_PROXYLESS->value => GeeTestTask::class,
        TypeTask::TURNSTILE_TASK->value => TurnstileTask::class,
        TypeTask::TURNSTILE_CHALLENGE_TASK->value => TurnstileChallengeTask::class,
        TypeTask::TURNSTILE_WAITING_ROOM_TASK->value => TurnstileWaitingRoomTask::class,
        TypeTask::COMPLEX_IMAGE_TASK->value => ComplexImageTask::class,
        TypeTask::DATADOME_TASK->value => DataDomeTask::class,
        TypeTask::BASILISK_TASK->value => BasiliskTask::class,
        TypeTask::TENDI_TASK->value => TenDITask::class,
        TypeTask::AMAZON_TASK->value => AmazonTask::class,
        TypeTask::BINANCE_TASK->value => BinanceTask::class,
        TypeTask::IMPERVA_TASK->value => ImpervaTask::class,
        TypeTask::PROSOPO_TASK->value => ProsopoTask::class,
        TypeTask::YIDUN_TASK->value => YidunTask::class,
        TypeTask::MT_CAPTCHA_TASK->value => MTCaptchaTask::class,
        TypeTask::ALTCHA_TASK->value => AltchaTask::class,
        TypeTask::CASTLE_TASK->value => CastleTask::class,
        TypeTask::TSPD_TASK->value => TSPDTask::class,
        TypeTask::HUNT_TASK->value => HuntTask::class,
        TypeTask::ALIBABA_TASK->value => AlibabaTask::class,
    ];
}
