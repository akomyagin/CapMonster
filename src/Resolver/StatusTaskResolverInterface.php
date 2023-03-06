<?php

declare(strict_types=1);

namespace CapMonsterClient\Resolver;

use CapMonsterClient\Enum\StatusTask;
use CapMonsterClient\LibException\EnumResolverException;

interface StatusTaskResolverInterface
{
    /**
     * @throws EnumResolverException
     */
    public static function resolve(string $status): StatusTask;
}