<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Solution;

final class ReCaptchaSolution extends AbstractSolution
{
    /*
     * Хеш который необходимо подставить в форму с ReCaptcha2 в <textarea id="g-recaptcha-response" ..></textarea> .
     * Имеет длину от 500 до 2190 байт.
     */

    public function __construct(
        private readonly string $gRecaptchaResponse
    ) {
    }

    public function getGRecaptchaResponse(): string
    {
        return $this->gRecaptchaResponse;
    }
}