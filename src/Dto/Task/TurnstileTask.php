<?php

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;

final class TurnstileTask extends AbstractTask
{
    /**
     * Поддерживаются все подтипы Turnstile автоматически: manual, non-interactive и invisible.
     * Поэтому нет необходимости указывать подтип.
     * Такая задача будет выполняться нашим сервисом с использованием наших собственных прокси-серверов.
     *
     * параметр websiteUrl имя websiteURL тип String обязательно
     * Адрес страницы на которой решается каптча
     *
     * параметр websiteKey тип String обязательно
     * Ключ Turnstile
     */

    public function __construct(
        string $websiteUrl,
        string $websiteKey,
        ?ProxySetting $proxySetting = null
    ) {
        parent::__construct(
            ($proxySetting === null) ? TypeTask::TURNSTILE_TASK_PROXYLESS : TypeTask::TURNSTILE_TASK,
            $websiteUrl, $websiteKey
        );
    }
}