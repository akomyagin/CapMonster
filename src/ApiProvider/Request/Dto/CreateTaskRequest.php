<?php

declare(strict_types = 1);

namespace CapMonsterClient\ApiProvider\Request\Dto;

use CapMonsterClient\Common\Dto\Request\AbstractRequest;
use CapMonsterClient\Dto\Task\AbstractTask;
use CapMonsterClient\Enum\ApiMethod;

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

    public function getMethod(): ApiMethod
    {
        return ApiMethod::CREATE_TASK;
    }
}