<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

final class HCaptchaTask extends AbstractHCaptchaTask
{
    /*
     *
     * Объект содержит данные о задаче на решение hCaptcha.
     * Такая задача будет выполняться нашим сервисом с использованием наших собственных прокси-серверов.
     *
     * параметр websiteUrl имя websiteURL тип String обязательно
     * Адрес страницы на которой решается каптча
     *
     * параметр websiteKey тип String обязательно
     * Ключ-идентификатор hCaptcha на целевой странице.
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
     * параметр isInvisible тип Bool не обязательно
     * true, если hCaptcha невидимая
     *
     * параметр data тип String не обязательно
     * Дополнительный параметр, используемый в основном с isInvisible=true.
     * Важно: При передаче параметра обязательна передача userAgent.
     * Значение, которое передается в userAgent, должно соответствовать тому, которое используется для сабмита токена
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
        ?bool $isInvisible = null,
        ?string $data = null,
        ?string $userAgent = null,
        ?string $cookies = null,
    ) {
        parent::__construct($websiteUrl, $websiteKey, $isInvisible, $data, $userAgent, $cookies);
        $this->proxyTraitInit($proxyType, $proxyAddress, $proxyPort, $proxyLogin, $proxyPassword);
    }
}