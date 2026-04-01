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

    public function getErrorId(): int
    {
        return (int) $this->errorId;
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
