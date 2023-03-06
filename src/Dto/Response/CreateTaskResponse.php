<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Response;

final class CreateTaskResponse extends AbstractResponse
{
    public function __construct(
        private readonly int $taskId,
        $errorId,
        $errorCode
    ) {
        parent::__construct($errorId, $errorCode);
    }

    public function getTaskId(): int
    {
        return $this->taskId;
    }
}