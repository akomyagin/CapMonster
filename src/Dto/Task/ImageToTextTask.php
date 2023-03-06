<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;

final class ImageToTextTask extends AbstractTask
{
    /*
     * решение обычной капчи с текстом
     *
     * параметр body тип String обязательно
     * Содержимое файла капчи закодированное в base64. Убедитесь что шлете его без переносов строки.
     *
     * параметр сapMonsterModule имя CapMonsterModule тип String не обязательно
     * Имя модуля, например “yandex“ (yandex, special и другие). Альтернативный способ передачи имени модуля
     * и список всех доступных модулей можно найти здесь https://capmonster.atlassian.net/wiki/spaces/APIS/pages/187006977/CapMonster+Cloud+ApiKey
     *
     * параметр recognizingThreshold тип int не обязательно
     * Порог распознавания капчи с возможным значением от 0 до 100.
     * Например, если в систему было отправлено значение 90, и задача решилась с уверенностью 80,
     * то деньги за решение не спишутся. В этом случае пользователь получит ответ ERROR_CAPTCHA_UNSOLVABLE.
     *
     * параметр case имя Case тип Boolean не обязательно
     * Учитывать регистр при решении или нет.
     *
     * параметр numeric тип Int не обязательно
     * 0, 1 (1 - если капча состоит только из цифр)
     *
     * параметр math тип Boolean не обязательно
     * false — не определено
     * true — капча требует совершения математического действия (например: капча 2 + 6 = вернёт значение 8)
     */

    public function __construct(
        private readonly string $body,
        private readonly ?string $capMonsterModule = null,
        private readonly ?int $recognizingThreshold = null,
        private readonly ?bool $case = null,
        private readonly ?int $numeric = null,
        private readonly ?bool $math = null
    ) {
        parent::__construct(TypeTask::IMAGE_TO_TEXT_TASK);
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getCapMonsterModule(): ?string
    {
        return $this->capMonsterModule;
    }

    public function getRecognizingThreshold(): ?int
    {
        return $this->recognizingThreshold;
    }

    public function getCase(): ?bool
    {
        return $this->case;
    }

    public function getNumeric(): ?int
    {
        return $this->numeric;
    }

    public function getMath(): ?bool
    {
        return $this->math;
    }
}