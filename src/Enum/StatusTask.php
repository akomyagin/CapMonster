<?php

declare(strict_types=1);

namespace CapMonsterClient\Enum;

use CapMonsterClient\LibException\EnumResolverException;
use CapMonsterClient\Resolver\StatusTaskResolverInterface;

enum StatusTask: string implements EnumDescriptionInterface, StatusTaskResolverInterface
{
    case PROCESSING = 'processing';

    case READY = 'ready';

    public function description(): string
    {
        return match ($this) {
            StatusTask::PROCESSING => 'Задача в процессе выполнения',
            StatusTask::READY => 'Задача выполнена'
        };
    }

    public static function resolve(string $status): StatusTask
    {
        if($enum = StatusTask::tryFrom($status)) {

            return $enum;
        }

        throw new EnumResolverException(sprintf('Status %s is not resolve in StatusTask enum', $status));
    }
}
