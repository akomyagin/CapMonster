<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;

final class HCaptchaTask extends AbstractTask
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

    public function __construct(
        string $websiteUrl,
        string $websiteKey,
        private readonly ?bool $isInvisible = null,
        private readonly ?string $data = null,
        ?string $userAgent = null,
        ?string $cookies = null,
        ?ProxySetting $proxySetting = null
    ) {
        parent::__construct(
            ($proxySetting === null) ? TypeTask::H_CAPTCHA_TASK_PROXYLESS : TypeTask::H_CAPTCHA_TASK,
            $websiteUrl, $websiteKey, $userAgent, $cookies, $proxySetting
        );
    }

    public function getIsInvisible(): ?bool
    {
        return $this->isInvisible;
    }

    public function getData(): ?string
    {
        return $this->data;
    }
}