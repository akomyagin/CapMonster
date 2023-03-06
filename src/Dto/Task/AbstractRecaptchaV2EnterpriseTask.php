<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;

abstract class AbstractRecaptchaV2EnterpriseTask extends AbstractTask
{
    /*
     *
     * Объект содержит данные о задаче на решение reCAPTCHA Enterprise от Google.
     * Такая задача будет выполняться нашим сервисом с использованием наших собственных прокси-серверов.
     *
     * параметр websiteUrl имя websiteURL тип String обязательно
     * Адрес страницы на которой решается каптча
     *
     * параметр websiteKey тип String обязательно
     * Ключ-идентификатор ReCaptcha на целевой странице.
     * <div class="g-recaptcha" data-sitekey="ВОТ_ЭТОТ"></div>
     *
     * параметр enterprisePayload тип String не обязательно
     * Некоторые реализации виджета reCAPTCHA Enterprise могут содержать дополнительное поле s в структуре,
     * которая передаётся в метод grecaptcha.enterprise.render вместе с sitekey.
     *
     * Например:
     * 2JvUXHNTnZl1Jb6WEvbDyBMzrMTR7oQ78QRhBcG07rk9bpaAaE0LRq1ZeP5NYa0N из:
     * grecaptcha.enterprise.render("some-div-id", {
     * sitekey: "6Lc_aCMTAAAAABx7u2N0D1XnVbI_v6ZdbM6rYf16",
     * theme: "dark",
     * s: "2JvUXHNTnZl1Jb6WEvbDyBMzrMTR7oQ78QRhBcG07rk9bpaAaE0LRq1ZeP5NYa0N...ugQA"
     * });
     * параметр apiDomain тип String не обязательно
     * Адрес домена с которого загружать reCAPTCHA Enterprise. Например:
     * www.google.com
     * www.recaptcha.net
     * Не используйте параметр, если не знаете зачем он нужен.
     *
     * параметр userAgent тип String не обязательно
     * User-Agent браузера, используемый в эмуляции. Необходимо использовать подпись современного браузера,
     * иначе Google будет возвращать ошибку, требуя обновить браузер.
     *
     * параметр cookies тип String не обязательно
     * Дополнительные cookies которые мы должны использовать во время взаимодействия с целевой страницей.
     * Формат: cookiename1=cookievalue1; cookiename2=cookievalue2
     */

    use ReCaptchaWebTrait;

    public function __construct(
        string $websiteUrl,
        string $websiteKey,
        private readonly ?string $enterprisePayload = null,
        private readonly ?string $apiDomain = null,
        ?string $userAgent = null,
        ?string $cookies = null,
        $type = TypeTask::RECAPTCHA_V2_ENTERPRISE_TASK
    ) {
        parent::__construct($type);
        $this->webTraitInit($websiteUrl, $websiteKey, $userAgent, $cookies);
    }

    public function getEnterprisePayload(): ?string
    {
        return $this->enterprisePayload;
    }

    public function getApiDomain(): ?string
    {
        return $this->apiDomain;
    }
}