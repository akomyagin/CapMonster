<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Solution;

use JMS\Serializer\Annotation as Serializer;

final class GeeTestSolution extends AbstractSolution
{
    public function __construct(
        #[Serializer\SerializedName(name: 'challenge')]
        #[Serializer\Type('string')]
        private readonly ?string $challenge = null,
        #[Serializer\SerializedName(name: 'validate')]
        #[Serializer\Type('string')]
        private readonly ?string $validate = null,
        #[Serializer\SerializedName(name: 'seccode')]
        #[Serializer\Type('string')]
        private readonly ?string $seccode = null,
        #[Serializer\SerializedName(name: 'captcha_id')]
        #[Serializer\Type('string')]
        private readonly ?string $captchaId = null,
        #[Serializer\SerializedName(name: 'lot_number')]
        #[Serializer\Type('string')]
        private readonly ?string $lotNumber = null,
        #[Serializer\SerializedName(name: 'pass_token')]
        #[Serializer\Type('string')]
        private readonly ?string $passToken = null,
        #[Serializer\SerializedName(name: 'gen_time')]
        #[Serializer\Type('string')]
        private readonly ?string $genTime = null,
        #[Serializer\SerializedName(name: 'captcha_output')]
        #[Serializer\Type('string')]
        private readonly ?string $captchaOutput = null
    ) {
    }

    public function getChallenge(): ?string
    {
        return $this->challenge;
    }

    public function getValidate(): ?string
    {
        return $this->validate;
    }

    public function getSeccode(): ?string
    {
        return $this->seccode;
    }

    public function getCaptchaId(): ?string
    {
        return $this->captchaId;
    }

    public function getLotNumber(): ?string
    {
        return $this->lotNumber;
    }

    public function getPassToken(): ?string
    {
        return $this->passToken;
    }

    public function getGenTime(): ?string
    {
        return $this->genTime;
    }

    public function getCaptchaOutput(): ?string
    {
        return $this->captchaOutput;
    }
}
