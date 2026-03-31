<?php

declare(strict_types=1);

namespace CapMonsterClient\External\Dto\Response;

use JMS\Serializer\Annotation as Serializer;

final class GetBalanceResponse extends AbstractResponse
{
    public function __construct(
        #[Serializer\SerializedName(name: 'balance')]
        #[Serializer\Type(name: 'float')]
        private readonly float $balance
    ) {
    }

    public function getBalance(): float
    {
        return $this->balance;
    }
}