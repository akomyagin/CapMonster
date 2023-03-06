<?php

namespace CapMonsterClient\Dto\Task;

trait ReCaptchaWebTrait
{
    /*
     *
     * Объект содержит данные о задаче на решение ReCaptcha от Google.
     * Такая задача будет выполняться нашим сервисом с использованием наших собственных прокси-серверов.
     *
     * параметр websiteUrl имя websiteURL тип String обязательно
     * Адрес страницы на которой решается каптча
     *
     * параметр websiteKey тип String обязательно
     * Ключ-идентификатор ReCaptcha на целевой странице.
     * <div class="g-recaptcha" data-sitekey="ВОТ_ЭТОТ"></div>
     *
     * параметр userAgent тип String не обязательно
     * User-Agent браузера, используемый в эмуляции. Необходимо использовать подпись современного браузера,
     * иначе Google будет возвращать ошибку, требуя обновить браузер.
     *
     * параметр cookies тип String не обязательно
     * Дополнительные cookies которые мы должны использовать во время взаимодействия с целевой страницей.
     * Формат: cookiename1=cookievalue1; cookiename2=cookievalue2
     */

    private readonly string $websiteUrl;

    private readonly string $websiteKey;

    private readonly ?string $userAgent;

    private readonly ?string $cookies;

    private function webTraitInit(
        string $websiteUrl,
        string $websiteKey,
        ?string $userAgent = null,
        ?string $cookies = null
    ) {
        $this->websiteUrl = $websiteUrl;
        $this->websiteKey = $websiteKey;
        $this->userAgent = $userAgent;
        $this->cookies = $cookies;
    }

    public function getWebsiteUrl(): string
    {
        return $this->websiteUrl;
    }

    public function getWebsiteKey(): string
    {
        return $this->websiteKey;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function getCookies(): ?string
    {
        return $this->cookies;
    }
}