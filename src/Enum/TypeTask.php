<?php

declare(strict_types=1);

namespace CapMonsterClient\Enum;

enum TypeTask: string
{
    case IMAGE_TO_TEXT_TASK = 'ImageToTextTask';

    case NO_CAPTCHA_TASK = 'NoCaptchaTask';

    case NO_CAPTCHA_TASK_PROXYLESS = 'NoCaptchaTaskProxyless';

    case RECAPTCHA_V3_TASK_PROXYLESS = 'RecaptchaV3TaskProxyless';

    case RECAPTCHA_V2_ENTERPRISE_TASK = 'RecaptchaV2EnterpriseTask';

    case RECAPTCHA_V2_ENTERPRISE_TASK_PROXYLESS = 'RecaptchaV2EnterpriseTaskProxyless';

    case RECAPTCHA_V3_ENTERPRISE_TASK = 'RecaptchaV3EnterpriseTask';

    case RECAPTCHA_V3_ENTERPRISE_TASK_PROXYLESS = 'RecaptchaV3EnterpriseTaskProxyless';

    case FUN_CAPTCHA_TASK = 'FunCaptchaTask';

    case FUN_CAPTCHA_TASK_PROXYLESS = 'FunCaptchaTaskProxyless';

    case H_CAPTCHA_TASK = 'HCaptchaTask';

    case H_CAPTCHA_TASK_PROXYLESS = 'HCaptchaTaskProxyless';

    case GEE_TEST_TASK = 'GeeTestTask';

    case GEE_TEST_TASK_PROXYLESS = 'GeeTestTaskProxyless';

    case TURNSTILE_TASK = 'TurnstileTask';

    case TURNSTILE_CHALLENGE_TASK = 'TurnstileChallengeTask';

    case TURNSTILE_WAITING_ROOM_TASK = 'TurnstileWaitingRoomTask';

    case COMPLEX_IMAGE_TASK = 'ComplexImageTask';

    case DATADOME_TASK = 'DataDomeTask';

    case BASILISK_TASK = 'BasiliskTask';

    case TENDI_TASK = 'TenDITask';

    case AMAZON_TASK = 'AmazonTask';

    case BINANCE_TASK = 'BinanceTask';

    case IMPERVA_TASK = 'ImpervaTask';

    case PROSOPO_TASK = 'ProsopoTask';

    case YIDUN_TASK = 'YidunTask';

    case MT_CAPTCHA_TASK = 'MTCaptchaTask';

    case ALTCHA_TASK = 'AltchaTask';

    case CASTLE_TASK = 'CastleTask';

    case TSPD_TASK = 'TSPDTask';

    case HUNT_TASK = 'HuntTask';

    case ALIBABA_TASK = 'AlibabaTask';
}