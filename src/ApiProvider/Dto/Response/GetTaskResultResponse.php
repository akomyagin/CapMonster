<?php

declare(strict_types=1);

namespace CapMonsterClient\ApiProvider\Dto\Response;

use CapMonsterClient\Common\Exception\CapMonsterException;
use CapMonsterClient\Common\Exception\EnumResolverException;
use CapMonsterClient\Common\Exception\ExceptionFactory;
use CapMonsterClient\Enum\ErrorType;
use CapMonsterClient\Enum\StatusTask;
use JMS\Serializer\Annotation as Serializer;

final class GetTaskResultResponse extends AbstractResponse
{
    /**
     * Kept as a raw string so an unknown/missing status from the API cannot
     * blow up deserialization with a raw \ValueError; {@see self::getStatus()}
     * resolves it lazily into the {@see StatusTask} enum.
     */
    #[Serializer\SerializedName(name: 'status')]
    #[Serializer\Type(name: 'string')]
    private readonly ?string $status;

    /**
     * @var array<string, mixed>
     */
    #[Serializer\SerializedName(name: 'solution')]
    #[Serializer\Type(name: 'array')]
    private readonly ?array $solution;

    /**
     * @throws CapMonsterException when the API returned an unknown or missing status
     */
    public function getStatus(): StatusTask
    {
        try {
            return StatusTask::resolve($this->status ?? '');
        } catch (EnumResolverException $exception) {
            throw ExceptionFactory::fromErrorType(ErrorType::RESPONSE_ERROR, $exception);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function getSolution(): array
    {
        return $this->solution ?? [];
    }
}
