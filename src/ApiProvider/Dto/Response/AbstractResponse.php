<?php

declare(strict_types=1);

namespace CapMonsterClient\ApiProvider\Dto\Response;

use JMS\Serializer\Annotation as Serializer;

abstract class AbstractResponse
{
    #[Serializer\SerializedName("errorId")]
    #[Serializer\Type("integer")]
    private readonly ?int $errorId;

    #[Serializer\SerializedName("errorCode")]
    #[Serializer\Type("string")]
    private readonly ?string $errorCode;

    /**
     * Null coalescing keeps this safe even when the API omits "errorId"
     * and JMS leaves the readonly property uninitialized.
     */
    public function getErrorId(): int
    {
        return (int) ($this->errorId ?? 0);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode ?? '';
    }

    public function isError(): bool
    {
        return $this->getErrorId() > 0 || !empty($this->getErrorCode());
    }
}
