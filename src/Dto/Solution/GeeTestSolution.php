<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Solution;

final class GeeTestSolution extends AbstractSolution
{
    /*
     * Все три параметра необходимы при отправке формы на целевом сайте.
     */

    public function __construct(
        private readonly string $challenge,
        private readonly string $validate,
        private readonly string $seccode
    ) {
    }

    public function getChallenge(): string
    {
        return $this->challenge;
    }

    public function getValidate(): string
    {
        return $this->validate;
    }

    public function getSeccode(): string
    {
        return $this->seccode;
    }
}