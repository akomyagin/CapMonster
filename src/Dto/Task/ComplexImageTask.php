<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;

final class ComplexImageTask extends AbstractTask
{
    public function __construct(
        private readonly string $body,
        private readonly ?string $module = null
    ) {
        parent::__construct(TypeTask::COMPLEX_IMAGE_TASK, '', '');
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getModule(): ?string
    {
        return $this->module;
    }
}
