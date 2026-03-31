<?php

declare(strict_types=1);

namespace CapMonsterClient\Common\Exception;

use CapMonsterClient\Enum\ErrorType;
use Exception;
use Psr\Http\Client\ClientExceptionInterface;

final class CapMonsterException extends Exception implements ClientExceptionInterface
{
    public function __construct(ErrorType $type, Exception $previousException = null)
    {
        parent::__construct(
            message: $type->description(),
            code: $previousException ? $previousException->getCode() : 0,
            previous: $previousException
        );
    }
}