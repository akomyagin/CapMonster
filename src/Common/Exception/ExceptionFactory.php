<?php

declare(strict_types=1);

namespace CapMonsterClient\Common\Exception;

use CapMonsterClient\Enum\ErrorType;
use Exception;

final class ExceptionFactory
{
    public static function fromErrorType(ErrorType $type, ?Exception $previous = null): CapMonsterException
    {
        return match ($type) {
            ErrorType::INVALID_KEY,
            ErrorType::REQUEST_IS_NOT_ALLOWED_FROM_YOUR_IP,
            ErrorType::IP_BANNED,
            ErrorType::YOUR_IP_IS_BLOCKED => new AuthException($type, $previous),
            ErrorType::NO_FUNDS => new BalanceException($type, $previous),
            ErrorType::FAILED_TO_CONNECT_PROXY,
            ErrorType::THE_PROXY_IP_IS_BANNED,
            ErrorType::PROXY_MISSING,
            ErrorType::PROXY_NOT_AUTHORISED,
            ErrorType::PROXY_READ_TIMEOUT => new ProxyException($type, $previous),
            ErrorType::INCORRECT_TASK_TYPE,
            ErrorType::TASK_ABSENT,
            ErrorType::INVALID_RECAPTCHA_SITEKEY,
            ErrorType::INVALID_RECAPTCHA_DOMAIN => new TaskValidationException($type, $previous),
            ErrorType::UNKNOWN_ERROR,
            ErrorType::SEND_MESSAGE_ERROR,
            ErrorType::RESPONSE_CODE_ERROR,
            ErrorType::RESPONSE_ERROR,
            ErrorType::TYPE_TASK_RESOLVE_EXCEPTION,
            ErrorType::INVALID_ARGUMENT_EXCEPTION,
            ErrorType::TIMEOUT_EXPIRED,
            ErrorType::REQUEST_LIMIT_EXCEEDED,
            ErrorType::CAPTCHA_IS_NOT_READY,
            ErrorType::CAPTCHA_ID_IS_NOT_FOUND,
            ErrorType::CAPTCHA_ID_IS_NOT_FOUND_2,
            ErrorType::CAPTCHA_UNSOLVABLE,
            ErrorType::BIG_IMAGE_SIZE,
            ErrorType::ZERO_IMAGE_SIZE,
            ErrorType::INCORRECT_METHOD,
            ErrorType::THE_DOMAIN_IS_NOT_ALLOWED,
            ErrorType::THE_TOKEN_IS_EXPIRED,
            ErrorType::NO_FREE_SERVERS,
            ErrorType::RECAPTCHA_TIMEOUT,
            ErrorType::WRONG_USERAGENT => new RuntimeCapMonsterException($type, $previous),
        };
    }
}
