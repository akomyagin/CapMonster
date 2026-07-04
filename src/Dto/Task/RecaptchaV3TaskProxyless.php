<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;
use JMS\Serializer\Annotation as Serializer;

final class RecaptchaV3TaskProxyless extends AbstractTask
{
    /*
     *
     * Объект содержит данные о задаче на решение ReCaptcha3 от Google.
     * Такая задача будет выполняться нашим сервисом с использованием наших собственных прокси-серверов.
     * При создании задачи, в отличии от ReCaptcha2, необходимо дополнительно передавать два параметра - pageAction и minScore.
     *
     * параметр websiteUrl имя websiteURL тип String обязательно
     * Адрес страницы на которой решается каптча
     *
     * параметр websiteKey тип String обязательно
     * Ключ-идентификатор ReCaptcha на целевой странице.
     * <div class="g-recaptcha" data-sitekey="ВОТ_ЭТОТ"></div>
     *
     * параметр minScore тип Double не обязательно
     * Может иметь значение от 0.1 до 0.9.
     *
     * параметр pageAction тип String не обязательно
     * Значение параметра action, которое передаётся виджетом ReCaptcha в Google, и которое потом видит владелец сайта
     * при проверке токена. Значение по-умолчанию: verify
     * Пример в html:
     * grecaptcha.execute('site_key', {action:'login_test'}).
     */

    public function __construct(
        string $websiteUrl,
        string $websiteKey,
        #[Serializer\SerializedName('minScore')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?float $minScore = null,
        #[Serializer\SerializedName('pageAction')]
        #[Serializer\SkipWhenEmpty()]
        private readonly ?string $pageAction = null,
    )
    {
        parent::__construct(TypeTask::RECAPTCHA_V3_TASK_PROXYLESS, $websiteUrl, $websiteKey);
    }

    public function getMinScore(): ?float
    {
        return $this->minScore;
    }

    public function getPageAction(): ?string
    {
        return $this->pageAction;
    }
}