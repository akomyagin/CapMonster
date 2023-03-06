<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

final class RecaptchaV2EnterpriseTask extends AbstractRecaptchaV2EnterpriseTask
{
    /*
     *
     * Объект содержит данные о задаче на решение ReCaptcha2 от Google версии Enterprise.
     * Для обеспечения универсальности решения этого вида каптчи нам необходимо использовать все данные,
     * которые Вы используете во время автоматизации заполнения формы на целевом сайте, включая прокси,
     * user-agent браузера и cookies. Это позволит избежать любых проблем при изменении Google кода своей каптчи.
     *
     * Каптча может решаться довольно долго по сравнению с обычной каптчей, но это компенсируется тем,
     * что полученный g-captcha-response действует еще 60 секунд после решения каптчи.
     *
     * Внимание!
     * Если прокси с авторизацией по IP, то необходимо обязательно добавить 116.203.55.208 в белый список.
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
        ?string $enterprisePayload = null,
        ?string $apiDomain = null,
        ?string $userAgent = null,
        ?string $cookies = null
    ) {
        parent::__construct($websiteUrl, $websiteKey, $enterprisePayload, $apiDomain, $userAgent, $cookies);
        $this->proxyTraitInit($proxyType, $proxyAddress, $proxyPort, $proxyLogin, $proxyPassword);
    }
}