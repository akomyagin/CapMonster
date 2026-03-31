<?php

declare(strict_types = 1);

namespace CapMonsterClient\ApiProvider\Request\Dto;

use CapMonsterClient\Common\Dto\Request\AbstractRequest;
use CapMonsterClient\Dto\Task\AbstractTask;
use CapMonsterClient\Enum\ApiMethod;

final class GetTaskResultRequest extends AbstractRequest
{
    public function __construct(
        string $clientKey,
        private readonly AbstractTask $task
    )
    {
        parent::__construct($clientKey);
    }

    public function getTaskId(): int
    {
        return $this->task->getTaskId();
    }
}