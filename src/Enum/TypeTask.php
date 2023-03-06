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

    case FUN_CAPTCHA_TASK = 'FunCaptchaTask';

    case FUN_CAPTCHA_TASK_PROXYLESS = 'FunCaptchaTaskProxyless';

    case H_CAPTCHA_TASK = 'HCaptchaTask';

    case H_CAPTCHA_TASK_PROXYLESS = 'HCaptchaTaskProxyless';
}