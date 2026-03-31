<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;

final class GeeTestTask extends AbstractTask
{
    /*
     *
     * Этот тип задач используется для решения каптчи GeeTest с использованием Ваших прокси.
     * Ваше приложение должно прислать адрес сайта, публичный ключ домена (gt), ключ (challenge) и прокси.
     *
     * Результатом решения задачи является три токена для сабмита формы.
     *
     * Внимание!
     * Прокси с авторизацией по IP пока не поддерживаются.
     *
     * параметр websiteUrl имя websiteURL тип String обязательно
     * Адрес страницы на которой решается каптча
     *
     * параметр gt тип String обязательно
     * Ключ-идентификатор GeeTest для домена. Статическое значение, редко обновляется.
     *
     * параметр challenge тип String обязательно
     * Меняющийся ключ.
     * При каждом обращении к нашему API нужно получать новое значение ключа.
     * Если каптча загружена на странице, то значение challenge уже недействительно и Вы получите в ответ
     * ошибку ERROR_TOKEN_EXPIRED.
     * За задачи с ошибкой ERROR_TOKEN_EXPIRED плата взимается как за успешно решённые задачи.
     * Нужно изучить запросы и найти тот, в котором возвращается это значение и перед каждым созданием задачи
     *  на распознавания выполнять этот запрос и парсить challenge из него.
     *
     * параметр geetestApiServerSubdomain тип String не обязательно
     * Необязательный параметр.
     * Может потребоваться для некоторых сайтов.
     *
     * параметр geetestGetLib тип String не обязательно
     * Необязательный параметр. Может потребоваться для некоторых сайтов.
     * Отправляйте JSON в виде строки.
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
     */

    public function __construct(
        string $websiteUrl,
        private readonly string $gt,
        private readonly string $challenge,
        private readonly ?string $geetestApiServerSubdomain = null,
        private readonly ?string $geetestGetLib = null,
        ?ProxySetting $proxySetting = null
    ) {
        parent::__construct(
            ($proxySetting === null) ? TypeTask::GEE_TEST_TASK_PROXYLESS : TypeTask::GEE_TEST_TASK,
            $websiteUrl, $gt, proxySetting: $proxySetting);
    }

    public function getGt(): string
    {
        return $this->gt;
    }

    public function getChallenge(): string
    {
        return $this->challenge;
    }

    public function getGeetestApiServerSubdomain(): ?string
    {
        return $this->geetestApiServerSubdomain;
    }

    public function getGeetestGetLib(): ?string
    {
        return $this->geetestGetLib;
    }
}