<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;
use JMS\Serializer\Annotation as Serializer;

final class YidunTask extends AbstractTask
{
    public function __construct(
        string $websiteUrl,
        string $websiteKey,
        #[Serializer\SerializedName('yidunGetLib')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?string $yidunGetLib = null,
        #[Serializer\SerializedName('yidunApiServerSubdomain')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?string $yidunApiServerSubdomain = null,
        #[Serializer\SerializedName('challenge')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?string $challenge = null,
        #[Serializer\SerializedName('hcg')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?string $hcg = null,
        #[Serializer\SerializedName('hct')]
        #[Serializer\Type('integer')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?int $hct = null,
        ?string $userAgent = null,
        ?string $cookies = null,
        ?ProxySetting $proxySetting = null
    ) {
        parent::__construct(TypeTask::YIDUN_TASK, $websiteUrl, $websiteKey, $userAgent, $cookies, $proxySetting);
    }

    public function getYidunGetLib(): ?string
    {
        return $this->yidunGetLib;
    }

    public function getYidunApiServerSubdomain(): ?string
    {
        return $this->yidunApiServerSubdomain;
    }

    public function getChallenge(): ?string
    {
        return $this->challenge;
    }

    public function getHcg(): ?string
    {
        return $this->hcg;
    }

    public function getHct(): ?int
    {
        return $this->hct;
    }
}
