<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;

final class FunCaptchaTask extends AbstractFunCaptchaTask
{
    /*
     *
     * Этот тип задач решает FunCaptcha.
     * Такая задача будет выполняться нашим сервисом с использованием наших собственных прокси-серверов.
     * Результатом решения задачи является токен для сабмита формы.
     *
     * параметр websiteUrl имя websiteURL тип String обязательно
     * Адрес страницы на которой решается каптча
     *
     * параметр websitePublicKey тип String обязательно
     * Ключ-идентификатор FunCaptcha на целевой странице.
     * Его можно найти в блоке <div id="funcaptcha" data-pkey="ВОТ_ЗДЕСЬ"></div>
     * или в значении элементов с именем fc-token и verification-token, после параметра pk=
     *
     * параметр funCaptchaApiJsSubdomain имя funcaptchaApiJSSubdomain тип String не обязательно
     * Специальный сервисный URL, с которого должен загружаться JS виджет каптчи.
     * Его можно найти в элементе с именем fc-token - значение после surl.
     * Оно требуется, если используется домен отличный от client-api.arkoselabs.com
     *
     * параметр data тип String не обязательно
     * Дополнительный параметр, который может требоваться для некоторых решений FunCaptcha.
     * Используйте это свойство для передачи параметра blob в виде массива, сведенного в строку. Пример:
     * {"\blob\":\"HERE_COMES_THE_blob_VALUE\"}
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
        string $websitePublicKey,
        string $proxyType,
        string $proxyAddress,
        int $proxyPort,
        ?string $proxyLogin = null,
        ?string $proxyPassword = null,
        ?string $funCaptchaApiJsSubdomain = null,
        ?string $data = null,
        ?string $userAgent = null,
        ?string $cookies = null
    ) {
        parent::__construct($websiteUrl, $websitePublicKey, $funCaptchaApiJsSubdomain, $data, $userAgent, $cookies);
        $this->proxyTraitInit($proxyType, $proxyAddress, $proxyPort, $proxyLogin, $proxyPassword);
    }
}