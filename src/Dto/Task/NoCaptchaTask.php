<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;
use JMS\Serializer\Annotation as Serializer;

final class NoCaptchaTask extends AbstractTask
{
    /*
     * решение каптчи Google
     *
     * Объект содержит данные о задаче на решение ReCaptcha2 от Google.
     * Такая задача будет выполняться нашим сервисом с использованием наших собственных прокси-серверов.
     *
     * параметр websiteURL тип String обязательно
     * Адрес страницы на которой решается каптча
     *
     * параметр websiteKey тип String обязательно
     * Ключ-идентификатор ReCaptcha2 на целевой странице.
     * <div class="g-recaptcha" data-sitekey="ВОТ_ЭТОТ"></div>
     *
     * параметр recaptchaDataSValue тип String не обязательно
     * Некоторые реализации виджета ReCaptcha2 могут содержать дополнительный параметр "data-s" в div'е ReCaptcha2,
     * который является одноразовым токеном и должен собираться каждый раз при решении ReCaptcha2.
     * <div class="g-recaptcha" data-sitekey="some sitekey" data-s="ВОТ_ЭТОТ"></div>
     *
     * параметр userAgent тип String не обязательно
     * User-Agent браузера, используемый в эмуляции. Необходимо использовать подпись современного браузера,
     * иначе Google будет возвращать ошибку, требуя обновить браузер.
     *
     * параметр cookies тип String не обязательно
     * Дополнительные cookies которые мы должны использовать во время взаимодействия с целевой страницей.
     * Формат: cookiename1=cookievalue1; cookiename2=cookievalue2
     */

    public function __construct(
        string $websiteUrl,
        string $websiteKey,
        #[Serializer\SerializedName('recaptchaDataSValue')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?string $recaptchaDataSValue = null,
        #[Serializer\SerializedName('isInvisible')]
        #[Serializer\Type('boolean')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?bool $isInvisible = null,
        ?string $userAgent = null,
        ?string $cookies = null,
        ?ProxySetting $proxySetting = null
    ) {
        parent::__construct(
            ($proxySetting === null) ? TypeTask::NO_CAPTCHA_TASK_PROXYLESS : TypeTask::NO_CAPTCHA_TASK,
            $websiteUrl, $websiteKey, $userAgent, $cookies, $proxySetting
        );
    }

    public function getRecaptchaDataSValue(): ?string
    {
        return $this->recaptchaDataSValue;
    }

    public function getIsInvisible(): ?bool
    {
        return $this->isInvisible;
    }
}