<?php

declare(strict_types = 1);

namespace CapMonsterClient\Dto\Request;

final class GetTaskResultRequest extends AbstractRequest
{
    public function __construct(
        string $clientKey,
        private readonly int $taskId
    )
    {
        parent::__construct($clientKey);
    }

    public function getTaskId(): int
    {
        return $this->taskId;
    }
}