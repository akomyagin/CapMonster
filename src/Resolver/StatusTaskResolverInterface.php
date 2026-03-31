<?php

declare(strict_types=1);

namespace CapMonsterClient\Resolver;

use CapMonsterClient\Common\Exception\EnumResolverException;
use CapMonsterClient\Enum\StatusTask;

interface StatusTaskResolverInterface
{
    /**
     * @throws EnumResolverException
     */
    public static function resolve(string $status): StatusTask;
}