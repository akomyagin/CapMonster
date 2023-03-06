<?php

declare(strict_types = 1);

namespace CapMonsterClient\Dto\Request;

use CapMonsterClient\Dto\Task\AbstractTask;

final class CreateTaskRequest extends AbstractRequest
{

    public function __construct(
        string $clientKey,
        private readonly AbstractTask $task,
        private readonly ?string $callbackUrl = null
    )
    {
        parent::__construct($clientKey);
    }

    public function getTask(): AbstractTask
    {
        return $this->task;
    }

    public function getCallbackUrl(): ?string
    {
        return $this->callbackUrl;
    }
}