<?php

declare(strict_types=1);

namespace CapMonsterClient\Resolver;

use CapMonsterClient\Enum\ErrorType;
use CapMonsterClient\External\Dto\Response\AbstractResponse;

final class ErrorResolver
{
    public static function resolve(AbstractResponse $response): ?ErrorType
    {
        if ($response->isError()) {
            return ErrorType::tryFrom($response->getErrorCode()) ?? ErrorType::UNKNOWN_ERROR;
        }

        return null;
    }
}