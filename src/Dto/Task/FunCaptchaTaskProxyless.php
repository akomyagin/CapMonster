<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;

final class FunCaptchaTaskProxyless extends AbstractFunCaptchaTask
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
     */

    public function __construct(
        string $websiteUrl,
        string $websitePublicKey,
        ?string $funCaptchaApiJsSubdomain = null,
        ?string $data = null
    ) {
        parent::__construct($websiteUrl, $websitePublicKey, $funCaptchaApiJsSubdomain, $data, type: TypeTask::FUN_CAPTCHA_TASK_PROXYLESS);
    }
}