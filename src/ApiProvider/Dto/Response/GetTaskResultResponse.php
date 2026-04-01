<?php

declare(strict_types=1);

namespace CapMonsterClient\ApiProvider\Dto\Response;

use CapMonsterClient\Enum\StatusTask;
use JMS\Serializer\Annotation as Serializer;

final class GetTaskResultResponse extends AbstractResponse
{
    #[Serializer\SerializedName(name: 'status')]
    #[Serializer\Type(name: 'enum<"CapMonsterClient\Enum\StatusTask">')]
    private readonly StatusTask $status;

    /**
     * @var array<string, mixed>
     */
    #[Serializer\SerializedName(name: 'solution')]
    #[Serializer\Type(name: 'array')]
    private readonly ?array $solution;

    public function getStatus(): StatusTask
    {
        return $this->status;
    }

    /**
     * @return array<string, mixed>
     */
    public function getSolution(): array
    {
        return $this->solution ?? [];
    }
}
