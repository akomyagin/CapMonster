<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;
use JMS\Serializer\Annotation as Serializer;

final class AmazonTask extends AbstractTask
{
    public function __construct(
        string $websiteUrl,
        string $websiteKey,
        #[Serializer\SerializedName('challengeScript')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?string $challengeScript = null,
        #[Serializer\SerializedName('captchaScript')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?string $captchaScript = null,
        #[Serializer\SerializedName('context')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?string $context = null,
        #[Serializer\SerializedName('iv')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?string $iv = null,
        #[Serializer\SerializedName('cookieSolution')]
        #[Serializer\Type('boolean')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?bool $cookieSolution = null,
        ?string $userAgent = null,
        ?string $cookies = null,
        ?ProxySetting $proxySetting = null
    ) {
        parent::__construct(TypeTask::AMAZON_TASK, $websiteUrl, $websiteKey, $userAgent, $cookies, $proxySetting);
    }

    public function getChallengeScript(): ?string
    {
        return $this->challengeScript;
    }

    public function getCaptchaScript(): ?string
    {
        return $this->captchaScript;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function getIv(): ?string
    {
        return $this->iv;
    }

    public function getCookieSolution(): ?bool
    {
        return $this->cookieSolution;
    }
}
