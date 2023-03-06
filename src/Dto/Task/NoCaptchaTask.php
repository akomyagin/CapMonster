<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;

final class NoCaptchaTask extends AbstractNoCaptchaTask
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
     * параметр proxyType тип String обязательно
     * http - обычный http/https прокси
     * https - попробуйте эту опцию только если "http" не работает (требуется для некоторых кастомных прокси)
     * socks4 - socks4 прокси
     * socks5 - socks5 прокси
     *
     * параметр proxyAddress тип String обязательно
     * IP адрес прокси IPv4/IPv6. Не допускается:
     *     использование имен хостов
     *     использование прозрачных прокси (там где можно видеть IP клиента)
     *     использование прокси на локальных машинах
     *
     * параметр proxyPort тип Integer обязательно
     * Порт прокси
     *
     * параметр proxyLogin тип String не обязательно
     * Логин прокси-сервера
     *
     * параметр proxyPassword тип String не обязательно
     * Пароль прокси-сервера
     *
     * параметр userAgent тип String не обязательно
     * User-Agent браузера, используемый в эмуляции. Необходимо использовать подпись современного браузера,
     * иначе Google будет возвращать ошибку, требуя обновить браузер.
     *
     * параметр cookies тип String не обязательно
     * Дополнительные cookies которые мы должны использовать во время взаимодействия с целевой страницей.
     * Формат: cookiename1=cookievalue1; cookiename2=cookievalue2
     */

    use ProxyTrait;

    public function __construct(
        string $websiteUrl,
        string $websiteKey,
        string $proxyType,
        string $proxyAddress,
        int $proxyPort,
        ?string $proxyLogin = null,
        ?string $proxyPassword = null,
        ?string $recaptchaDataSValue = null,
        ?string $userAgent = null,
        ?string $cookies = null
    ) {
        parent::__construct($websiteUrl, $websiteKey, $recaptchaDataSValue, $userAgent, $cookies);
        $this->proxyTraitInit($proxyType, $proxyAddress, $proxyPort, $proxyLogin, $proxyPassword);
    }
}