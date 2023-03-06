<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;

abstract class AbstractFunCaptchaTask extends AbstractTask
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
        private readonly string $websitePublicKey,
        private readonly ?string $funCaptchaApiJsSubdomain = null,
        private readonly ?string $data = null,
        ?string $userAgent = null,
        ?string $cookies = null,
        $type = TypeTask::FUN_CAPTCHA_TASK
    ) {
        parent::__construct($type);
        $this->webTraitInit($websiteUrl, $websitePublicKey, $userAgent, $cookies);
    }

    public function getWebsitePublicKey(): string
    {
        return $this->websitePublicKey;
    }

    public function getFunCaptchaApiJsSubdomain(): ?string
    {
        return $this->funCaptchaApiJsSubdomain;
    }

    public function getData(): ?string
    {
        return $this->data;
    }
}