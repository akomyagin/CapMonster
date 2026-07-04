<?php

declare(strict_types=1);

namespace CapMonsterClient\Common\Exception;

use CapMonsterClient\Enum\ErrorType;

final class ExceptionFactory
{
    public static function fromErrorType(ErrorType $type, ?\Throwable $previous = null): CapMonsterException
    {
        return new CapMonsterException($type, $previous);
    }
}
