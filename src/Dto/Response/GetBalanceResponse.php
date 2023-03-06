<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Response;

final class GetBalanceResponse extends AbstractResponse
{
    public function __construct(
        private readonly float $balance,
        $errorId,
        $errorCode
    ) {
        parent::__construct($errorId, $errorCode);
    }

    public function getBalance(): float
    {
        return $this->balance;
    }
}