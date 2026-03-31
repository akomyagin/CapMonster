<?php

declare(strict_types=1);

namespace CapMonsterClient\External\Dto\Response;

use JMS\Serializer\Annotation as Serializer;

final class CreateTaskResponse extends AbstractResponse
{
    #[Serializer\SerializedName(name: 'taskId')]
    #[Serializer\Type(name: 'integer')]
    private readonly ?int $taskId;

    public function __construct()
    {
    }

    public function getTaskId(): int
    {
        return $this->taskId ?? 0;
    }
}