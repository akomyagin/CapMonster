<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Solution;

use JMS\Serializer\Annotation as Serializer;

final class TokenSolution extends AbstractSolution
{
    public function __construct(
        #[Serializer\SerializedName(name: 'token')]
        #[Serializer\Type('string')]
        private readonly ?string $token = null,
        #[Serializer\SerializedName(name: 'userAgent')]
        #[Serializer\Type('string')]
        private readonly ?string $userAgent = null,
        #[Serializer\SerializedName(name: 'cf_clearance')]
        #[Serializer\Type('string')]
        private readonly ?string $cfClearance = null
    ) {
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function getCfClearance(): ?string
    {
        return $this->cfClearance;
    }
}
