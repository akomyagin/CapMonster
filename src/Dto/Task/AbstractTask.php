<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;

abstract class AbstractTask
{
    public function __construct(
        private readonly TypeTask $type
    ) {
    }

    public function getType(): TypeTask
    {
        return $this->type;
    }
}