<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;

final class NoCaptchaTaskProxyless extends AbstractNoCaptchaTask
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
        string $websiteURL,
        string $websiteKey,
        ?string $recaptchaDataSValue = null,
        ?string $userAgent = null,
        ?string $cookies = null
    ) {
        parent::__construct($websiteURL, $websiteKey, $recaptchaDataSValue, $userAgent, $cookies, TypeTask::NO_CAPTCHA_TASK_PROXYLESS);
    }
}