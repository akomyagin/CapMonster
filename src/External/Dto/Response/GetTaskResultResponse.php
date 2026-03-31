<?php

declare(strict_types=1);

namespace CapMonsterClient\External\Dto\Response;

use CapMonsterClient\Enum\StatusTask;
use JMS\Serializer\Annotation as Serializer;

final class GetTaskResultResponse extends AbstractResponse
{
    public function __construct(
        #[Serializer\SerializedName(name: 'status')]
        #[Serializer\Type(name: 'enum<"CapMonsterClient\Enum\StatusTask">')]
        private readonly StatusTask $status,
    ) {
    }

    public function getStatus(): StatusTask
    {
        return $this->status;
    }
}