<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Solution;

use JMS\Serializer\Annotation as Serializer;

final class HCaptchaSolution extends AbstractSolution
{
    /*
     * gRecaptchaResponse
     * Хеш который необходимо подставить в форму с hCaptcha.
     *
     * userAgent
     * Необходимо использовать при сабмите тот же User Agent, c которым решалась hCaptcha.
     *
     * respKey
     * Результат функции "window.hcaptcha.getRespKey()" когда она доступна.
     * Часть сайтов используют данное значение для дополнительной проверки.
     */

    public function __construct(
        #[Serializer\SerializedName(name: 'gRecaptchaResponse')]
        #[Serializer\Type('string')]
        private readonly string $gRecaptchaResponse,
        #[Serializer\SerializedName(name: 'userAgent')]
        #[Serializer\Type('string')]
        private readonly string $userAgent,
        #[Serializer\SerializedName(name: 'respKey')]
        #[Serializer\Type('string')]
        private readonly string $respKey
    ) {
    }

    public function getGRecaptchaResponse(): string
    {
        return $this->gRecaptchaResponse;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function getRespKey(): string
    {
        return $this->respKey;
    }
}